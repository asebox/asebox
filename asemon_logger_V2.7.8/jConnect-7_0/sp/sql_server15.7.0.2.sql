/* Master only */
/* Sccsid = "%Z% generic/sproc/%M% %I% %G%" */
/*
**  JDBC_INSTALL
**  This file contains the metadata Stored Procedures used by the JDBC drivers.
**
*/

/* 
** Confidential property of Sybase, Inc.
** (c) Copyright Sybase, Inc. 1998-2004.
** All rights reserved
*/

/* Don't delete the following line. It is the checkpoint for sed */
/* Server dependent stored procedure add here ADDPOINT_SP_COMMENT */
/*
** sql_server.sql
**
**
** Tables created:
**
**      Name                    Default Location
**      ----------------------- ----------------
**      spt_jdbc_table_types    master
**      spt_mda                 master
**      spt_jtext               master
**      spt_jdbc_conversion     master
**      spt_jdbc_datatype_info      sybsystemprocs
**
**
** Stored procedures created:
**
**      Name                          Default Location
**      ----------------------------- ----------------
**      sp_mda                        sybsystemprocs
**      sp_jdbc_datatype_info         sybsystemprocs
**      sp_jdbc_datatype_info_cts     sybsystemprocs
**      sp_jdbc_columns               sybsystemprocs
**      sp_jdbc_tables                sybsystemprocs
**      jdbc_function_escapes         sybsystemprocs
**      sp_jdbc_convert_datatype      sybsystemprocs
**      sp_jdbc_function_escapes      sybsystemprocs
**      sp_jdbc_fkeys                 sybsystemprocs
**      sp_jdbc_exportkey             sybsystemprocs
**      sp_jdbc_importkey             sybsystemprocs
**      sp_jdbc_getcrossreferences    sybsystemprocs
**      sp_jdbc_getschemas            sybsystemprocs
**      sp_jdbc_getcolumnprivileges   sybsystemprocs
**      sp_jdbc_gettableprivileges    sybsystemprocs
**      sp_jdbc_computeprivs          sybsystemprocs
**      sp_jdbc_getcatalogs           sybsystemprocs
**      sp_jdbc_primarykey            sybsystemprocs
**      sp_sql_type_name              sybsystemprocs
**      sp_jdbc_getbestrowidentifier  sybsystemprocs
**      sp_jdbc_getisolationlevels    sybsystemprocs
**      sp_jdbc_getindexinfo          sybsystemprocs
**      sp_jdbc_stored_procedures     sybsystemprocs
**      sp_jdbc_getprocedurecolumns   sybsystemprocs
**      sp_jdbc_getversioncolumns     sybsystemprocs
**      sp_jdbc_escapeliteralforlike  sybsystemprocs
**      sp_default_charset            sybsystemprocs
**      sp_jdbc_getudts               sybsystemprocs
**      sp_jdbc_getsupertypes         sybsystemprocs
**      sp_jdbc_getsupertables        sybsystemprocs
**      sp_jdbc_class_for_name        sybsystemprocs
**      sp_jdbc_jar_for_class         sybsystemprocs
**      sp_jdbc_jar_by_name           sybsystemprocs
**      sp_jdbc_classes_in_jar        sybsystemprocs
**      sp_drv_column_default         sybsystemprocs
**
**
** File Sections for use with the jConnect IsqlApp Sample:
**
**      Section Name  Description
**      ------------- ---------------------------------------
**      CLEANUP       Removes all of the tables/sprocs
**                    created by this script.
**
*/


set quoted_identifier on
go

/* Don't delete the following line. It is the checkpoint for sed */
/* Server dependent stored procedure add here ADDPOINT_SP_VERSION */

declare @retval int
exec @retval = sp_version 'installjdbc',NULL,'jConnect (TM) for JDBC(TM)/7.07 ESD #5 (Build 26792)/P/EBF20686/JDK 1.6.0/jdbcmain/OPT/Mon Oct 15 11:36:14 PDT 2012', 'start'
if (@retval != 0) select syb_quit()
go
/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

sp_configure 'allow updates', 1
go
/** SECTION END: CLEANUP **/

/* Common sp_drv_getcomment function applicable to sp_drv_bcpmetadata, and sp_drv_getprivileges in 15.7 only ADDPOINT_DRV_GETCOMMENTS */
IF EXISTS (SELECT * FROM sysobjects WHERE name = 'sp_drv_getcomment')
BEGIN
    DROP FUNCTION sp_drv_getcomment
END
GO

CREATE FUNCTION sp_drv_getcomment(@commentid INT) 
RETURNS VARCHAR(16384)
AS
BEGIN
    DECLARE @comment VARCHAR(16384)
    DECLARE @section VARCHAR(255)
    DECLARE  comment_iterator CURSOR
        FOR 
            SELECT 
                text 
            FROM 
                syscomments
            WHERE
                id = @commentid
            ORDER BY
                colid ASC
    OPEN comment_iterator
    FETCH comment_iterator INTO @section
    WHILE ( @@sqlstatus != 2 )
    BEGIN
        SELECT @comment = @comment + @section
        FETCH comment_iterator INTO @section
    END
    RETURN @comment
END
GO
GRANT EXECUTE ON sp_drv_getcomment TO public
GO
DUMP TRANSACTION master WITH truncate_only
GO
DUMP TRANSACTION sybsystemprocs WITH truncate_only
GO


/*
**   spt_jdbc_datatype_info
*/

/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if (exists (select * from sysobjects
		where name = 'spt_jdbc_datatype_info' and type = 'U'))
	drop table spt_jdbc_datatype_info
go
/** SECTION END: CLEANUP **/

create table spt_jdbc_datatype_info 
(
	ss_dtype	   tinyint	not null, 
	type_name          varchar(255)  null, 
	data_type          smallint     not null, 
	data_precision     int          null, 
	numeric_scale      smallint     null, 
	numeric_radix      smallint     null, 
	length             int          null, 
	literal_prefix     varchar(32 )  null, 
	literal_suffix     varchar(32 )  null, 
	create_params      varchar(32 )  null, 
	nullable           smallint     not null, 
	case_sensitive     smallint     not null, 
	searchable         smallint     not null, 
	unsigned_attribute smallint     null, 
	money              smallint     not null, 
	auto_increment     smallint     null, 
	local_type_name    varchar(128) not null, 
	aux                int          null,
	maximum_scale	   smallint	null,
	minimum_scale	   smallint	null,
	sql_data_type	   smallint	null,
	sql_datetime_sub   smallint	null,
	num_prec_radix	   smallint	null,
	interval_precision smallint	null
)
go

grant select on spt_jdbc_datatype_info to public
go

/*
**	There is a complicated set of SQL used to deal with
**	the SQL Server Null data types (MONEYn, INTn, etc.)
**	ISNULL is the only conditional SQL Server function that can be used
**	to differentiate between these types depending on size.
**
**	The aux column in the above table is used to differentiate
**	the null data types from the non-null types.
**
**	The aux column contains NULL for the null data types and 0
**	for the non-null data types.
**
**	The following SQL returns the contents of the aux column (0)
**	for the non-null data types and returns a variable non-zero
**	value for the null data types.
**
**			 ' I   I I FFMMDD'
**			 ' 1   2 4 484848'
**	isnull(d.aux, ascii(substring('666AAA@@@CB??GG', 
**	2*(d.ss_dtype%35+1)+2-8/c.length, 1))-60)
**
**	The '2*(d.ss_dtype%35+1)+2-8/c.length' selects a specific character of
**	the substring mask depending on the null data type and its size, i.e.
**	null MONEY4 or null MONEY8.  The character selected is then converted
**	to its binary value and an appropriate bias (i.e. 60) is subtracted to
**	return the correct non-zero value.	This value may be used as a
**	constant, i.e. ODBC data type, precision, scale, etc., or used as an
**	index with a substring to pick out a character string, i.e. type name.
**
**	The comments above the substring mask denote which character is
**	selected for each null data type, i.e. In (INTn), Fn (FLOATn), 
**	Mn (MONEYn) and Dn (DATETIMn).
**
**      Note that we don't need a row for DATEn and TIMEn because 
**      those additional entries are needed only when the datatype
**      can hold more than one usertype. For example, DATETIMEn can
**      hold both DATETIME and SMALLDATETIME data. 
*/


declare @case smallint

select @case = 0
select @case = 1 where 'a' != 'A'

/* Local Binary */
insert into spt_jdbc_datatype_info values
/* ss_type, name, data_type, prec, scale, rdx, len, prf, suf, 
** cp, nul, case, srch, unsigned, money, auto, local, aux 
** max_scale, min_scale, sql_data_typ, sql_datetime_sub, num_prec_radix,
** interval_precision
*/
(45, 'binary', -2, null, null, null, null, '0x', null, 
 'length', 1, 0, 2, null, 0, null, 'binary', 0,
 null, null, null, null, null, null)

/* Local Bit */
insert into spt_jdbc_datatype_info values
(50, 'bit', -7, 1, 0, 2, null, null, null, 
 null, 0, 0, 2, null, 0, null, 'bit', 0,
 null, null, null, null, null, null)

/* Local Char */
insert into spt_jdbc_datatype_info values
(47, 'char', 1, null, null, null, null, '''', '''', 
'length', 1, @case, 3, null, 0, null, 'char', 0,
 null, null, null, null, null, null)

/* Local Unichar */
insert into spt_jdbc_datatype_info values
(135, 'unichar', 1, null, null, null, null, '''', '''',
'length', 1, @case, 3, null, 0, null, 'unichar', 0,
 null, null, null, null, null, null)

/* Local Date */
insert into spt_jdbc_datatype_info values
(49, 'date', 91, 10, 0, 10, null, '''', '''',
null, 1, 0, 3, null, 0, null, 'date', 0,
null, null, 91, null, null, null)

/* Local Time */
insert into spt_jdbc_datatype_info values
(51, 'time', 92, 12, 3, 10, null, '''', '''',
null, 1, 0, 3, null, 0, null, 'time', 0,
null, null, 92, null, null, null)

/* Local Datetime */
insert into spt_jdbc_datatype_info values
(61, 'datetime', 93, 23, 3, 10, 16, '''', '''', 
 null, 1, 0, 3, null, 0, null, 'datetime', 0,
 null, null, 93, null, null, null)

/* Local Smalldatetime */
insert into spt_jdbc_datatype_info values
(58, 'smalldatetime', 93, 16, 0, 10, 16, '''', '''', 
null, 1, 0, 3, null, 0, null, 'smalldatetime', 0,
 null, null, 93, null, null, null)

/* Local Datetimn  sql server type is 'datetimn' */
insert into spt_jdbc_datatype_info values
(111, 'smalldatetime', 93, 0, 0, 10, 0, '''', '''', 
null, 1, 0, 3, null, 0, null, 'datetime', null,
 null, null, 93, null, null, null)

/* Decimal sql server type is 'decimal' */
insert into spt_jdbc_datatype_info values
(55, 'decimal', 3, 38, 0, 10, 0, null, null,
'precision,scale', 1, 0, 2, 0, 0, 0, 'decimal', 0,
 38, 0, null, null, null, null)

/* Numeric sql server type is 'numeric' */
insert into spt_jdbc_datatype_info values
(63, 'numeric', 2, 38, 0, 10, 0, null, null,
'precision,scale', 1, 0, 2, 0, 0, 0, 'numeric', 0,
 38, 0, null, null, null, null)

/* Local RealFloat   sql server type is 'floatn' */
insert into spt_jdbc_datatype_info values
(109, 'float        real', 1111, 0, null, 10, 0, null, null,
 null, 1, 0, 2, 0, 0, 0, 'real      float', null,
 null, null, null, null, 10, null)

/* Local Real */
insert into spt_jdbc_datatype_info values
(59, 'real', 7, 7, null, 10, null, null, null,
null, 1, 0, 2, 0, 0, 0, 'real', 0,
 null, null, null, null, 10, null)

/* Local Double */
insert into spt_jdbc_datatype_info values
(62, 'double precision', 8, 15, null, 10, null, null, null,
null, 1, 0, 2, 0, 0, 0, 'double precision', 0,
 null, null, null, null, 10, null)

/* Local Smallmoney */
insert into spt_jdbc_datatype_info values
(122, 'smallmoney', 3, 10, 4, 10, null, '$', null, 
null, 1, 0, 2, 0, 1, 0, 'smallmoney', 0,
 4, 4, null, null, null, null)

/* Local Int */
insert into spt_jdbc_datatype_info values
(56, 'int', 4, 10, 0, 10, null, null, null, 
null, 1, 0, 2, 0, 0, 0, 'int', 0,
 null, null, null, null, null, null)

/* Local Money */
insert into spt_jdbc_datatype_info values
(60, 'money', 3, 19, 4, 10, null, '$', null, 
null, 1, 0, 2, 0, 1, 0, 'money', 0,
 4, 4, null, null, null, null)

/* Local Moneyn  sql server type is 'moneyn'*/ 
insert into spt_jdbc_datatype_info values
(110, 'moneyn', 3, 0, 4, 10, 0, '$', null, 
null, 1, 0, 2, 0, 1, 0, 'moneyn', null,
 4, 4, null, null, null, null)

/* Local Smallint */
insert into spt_jdbc_datatype_info values
(52, 'smallint', 5, 5, 0, 10, null, null, null, 
null, 1, 0, 2, 0, 0, 0, 'smallint', 0,
 null, null, null, null, null, null)

/* Local Text */
insert into spt_jdbc_datatype_info values
(35, 'text', -1, 2147483647, null, null, 2147483647, '''', '''', 
null, 1, @case, 1, null, 0, null, 'text', 0,
 null, null, null, null, null, null)

/* Local Unitext */
insert into spt_jdbc_datatype_info values
(174, 'unitext', -1, 2147483647, null, null, 2147483647, '''', '''',
null, 1, @case, 1, null, 0, null, 'unitext', 0,
 null, null, null, null, null, null)

/* Java Object for ASE 12 */
insert into spt_jdbc_datatype_info values
(36, 'java.lang.Object', 1111, 2147483647, null, null, 2147483647, '''', '''', 
null, 1, @case, 1, null, 0, null, 'java.lang.Object', 0,
 null, null, null, null, null, null)

/* Local Varbinary */
insert into spt_jdbc_datatype_info values
(37, 'varbinary', -3, null, null, null, null, '0x', null, 
'max length', 1, 0, 2, null, 0, null, 'varbinary', 0,
 null, null, null, null, null, null)

/* Local Tinyint */
insert into spt_jdbc_datatype_info values
(48, 'tinyint', -6, 3, 0, 10, null, null, null, 
null, 1, 0, 2, 1, 0, 0, 'tinyint', 0,
 null, null, null, null, null, null)

/* Local Varchar */
insert into spt_jdbc_datatype_info values
(39, 'varchar', 12, null, null, null, null, '''', '''', 
'max length', 1, @case, 3, null, 0, null, 'varchar', 0,
 null, null, null, null, null, null)

/* Local Univarchar */
insert into spt_jdbc_datatype_info values
(155, 'univarchar', 12, null, null, null, null, '''', '''',
'max length', 1, @case, 3, null, 0, null, 'univarchar', 0,
 null, null, null, null, null, null)

/* Local Image */
insert into spt_jdbc_datatype_info values
(34, 'image', -4, 2147483647, null, null, 2147483647, '0x', null, 
null, 1, 0, 1, null, 0, null, 'image', 0,
 null, null, null, null, null, null)

/* Don't delete the following line. It is the checkpoint for sed */
/* Values for bigint and unsigned types for >=15.0 server  here ADDPOINT_BIGINT_UNSIGNED_TYPES*/
/* INT8 ->191 - bigint */
insert into sybsystemprocs.dbo.spt_jdbc_datatype_info values
(191, 'bigint', -5, 19, 0, 10, null, null, null,
 null, 1, 0, 2, 0, 0, 0, 'bigint', 0,
 null, null, null, null, null, null) 

/* UINT2 ->65 - unsigned smallint    */
insert into sybsystemprocs.dbo.spt_jdbc_datatype_info values
(65, 'unsigned smallint', 5, 5, 0, 10, null, null, null,
 null, 1, 0, 0, 0, 0, 0, 'usmallint', 0,
 null, null, null, null, null, null)

/* UINT4 ->66 - unsigned int     */
 insert into sybsystemprocs.dbo.spt_jdbc_datatype_info values
 (66, 'unsigned int', 4, 10, 0, 10, null, null, null,
  null, 1, 0, 0, 0, 0, 0, 'uint', 0,
  null, null, null, null, null, null)

/* UINT8 ->67 - unsigned bigint    */
insert into sybsystemprocs.dbo.spt_jdbc_datatype_info values 
(67, 'unsigned bigint', -5, 20, 0, 10, null, null, null,
 null, 1, 0, 0, 0, 0, 0, 'ubigint', 0,
 null, null, null, null, null, null)

/* Local Bigdatetime */
insert into spt_jdbc_datatype_info values
(189, 'bigdatetime', 11, 26, 6, 10, 16, '''', '''',
null, 1, 0, 3, null, 0, null, 'bigdatetime', 0,
null, null, 93, null, null, null)

/* Bigtime sql server type is "bigtime" */
insert into spt_jdbc_datatype_info values
(190, 'bigtime', 10, 15, 6, 10, null, '''', '''',
 null, 1, 0, 3, null, 0, null, 'bigtime', 0,
  null, null, 92, null, null, null)
go

dump tran sybsystemprocs with truncate_only
go

/*
**   End of spt_jdbc_datatype_info
*/



/*
**  sp_jdbc_escapeliteralforlike
*/

/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select *
	from sysobjects
		where name = 'sp_jdbc_escapeliteralforlike')
begin
	drop procedure sp_jdbc_escapeliteralforlike
end
go
/** SECTION END: CLEANUP **/


/*
** This is a utility procedure which takes an input string
** and places the escape character '\' before any symbol
** which needs to be a literal when used in a LIKE clause.
**
*/
create proc sp_jdbc_escapeliteralforlike @pString varchar(255) output
as
	declare @newString    varchar(255)
	declare @validEscapes varchar(255)
	declare @escapeChar   varchar(10)
	declare @pIndex       int
	declare @pLength      int
	declare @curChar      char(1)
	declare @escapeIndex  int
	declare @escapeLength int
	declare @boolEscapeIt int

	select @pLength = char_length(@pString)
	if (@pString is null) or (@pLength = 0)
	begin
		return
	end

	/*
	** we will use the backslash as our escape 
	** character
	*/
	select @escapeChar = '\'

	/* 
	** valid escape characters
	*/
	select @validEscapes = '%_\[]'
	select @escapeLength = char_length(@validEscapes)

	/* start at the beginning of the string */
	select @pIndex = 1
	select @newString = ''

	while(@pIndex <= @pLength)
	begin
		/*
		** get the next character of the string
		*/
		select @curChar = substring(@pString, @pIndex, 1)

		/*
		** loop through all of the escape characters and
		** see if the character needs to be escaped
		*/
		select @escapeIndex = 1
		select @boolEscapeIt = 0
		while(@escapeIndex <= @escapeLength)
		begin
			/* see if this is a match */
			if (substring(@validEscapes, @escapeIndex, 1) = 
				@curChar)
			begin
				select @boolEscapeIt = 1
				break
			end
			/* move on to the next escape character */
			select @escapeIndex = @escapeIndex + 1
		end

		/* build the string */
		if (@boolEscapeIt = 1)
		begin
			select @newString = @newString + @escapeChar + @curChar
		end
		else
		begin
			select @newString = @newString + @curChar
		end

		/* go on to the next character in our source string */
		select @pIndex = @pIndex + 1
	end

	/* return to new string to the caller */
	select @pString = ltrim(rtrim(@newString))
	return 0
go

exec sp_procxmode 'sp_jdbc_escapeliteralforlike', 'anymode'
go
grant execute on sp_jdbc_escapeliteralforlike to public
go
dump transaction sybsystemprocs with truncate_only 
go

/*
**  End of sp_jdbc_escapeliteralforlike
*/ 


/*Stored procedure to support CTS test suite  ADDPOINT_DATATYPE_INFO_CTS*/
/*
**  sp_jdbc_datatype_info_cts
*/

if exists (select * from sysobjects where name = 'sp_jdbc_datatype_info_cts')
    begin
	drop procedure sp_jdbc_datatype_info_cts
    end
go
/** SECTION END: CLEANUP **/

create procedure sp_jdbc_datatype_info_cts
as

declare @type_name 	varchar(32)
declare @data_type 	int
declare @precision	int
declare @literal_prefix	varchar(32)
declare @literal_suffix	varchar(32)
declare @create_params	varchar(32)
declare	@nullable	smallint
declare @case_sensitive	tinyint
declare @searchable	smallint
declare @unsigned_attribute	smallint
declare @fixed_prec_scale 	tinyint
declare @auto_increment		tinyint
declare	@local_type_name	varchar(32)
declare @minimum_scale	smallint
declare @maximum_scale	smallint
declare @sql_data_type	int
declare @sql_datetime_sub int
declare @num_prec_radix	int
declare @interval_precision int
declare @startedInTransaction       bit

if @@trancount = 0
begin
        set chained off
end

/* check if we're in a transaction, before we try any select statements */
if (@@trancount > 0)
  select @startedInTransaction = 1
else
  select @startedInTransaction = 0

set transaction isolation level 1

if (@startedInTransaction = 1)
   save transaction jdbc_keep_temptables_from_tx


/* this will make sure that all rows are sent even if
** the client "set rowcount" is differect
*/

set rowcount 0


create table #jdbc_datatype_info_cts
(
    TYPE_NAME    	varchar(32)	null,
    DATA_TYPE    	smallint	null,
    "PRECISION"    	int,
    LITERAL_PREFIX 	varchar(32)	null,
    LITERAL_SUFFIX  	varchar(32)	null,
    CREATE_PARAMS 	varchar(32)	null,
    NULLABLE      	smallint	null,
    CASE_SENSITIVE  	tinyint		null,
    SEARCHABLE     	smallint	null,
    UNSIGNED_ATTRIBUTE 	tinyint		null,
    FIXED_PREC_SCALE    tinyint		null,
    AUTO_INCREMENT     	tinyint		null, 
    LOCAL_TYPE_NAME    	varchar(32)	null,
    MINIMUM_SCALE      	smallint	null,
    MAXIMUM_SCALE     	smallint	null,
    SQL_DATA_TYPE     	int		null,
    SQL_DATETIME_SUB    int		null,
    NUM_PREC_RADIX     	int		null
    
)



    begin 
      declare jdbc_datatype_info_cursor1 cursor for
	select  /* Real SQL Server data types */
            case 
                when t.name = 'usmallint' then 'unsigned smallint'
                when t.name = 'uint' then 'unsigned int'
                when t.name = 'ubigint' then 'unsigned bigint'
            else
                t.name 
            end,
	      d.data_type ,
	      isnull(d.data_precision, convert(int,t.length)),
	      d.literal_prefix ,
	      d.literal_suffix ,
	      e.create_params ,
	      d.nullable ,
	      d.case_sensitive ,
	      d.searchable,
	      d.unsigned_attribute,
	      d.money,
	      d.auto_increment,
	      d.local_type_name,
	      d.minimum_scale,
	      d.maximum_scale,
	      d.sql_data_type,
	      d.sql_datetime_sub,
	      d.num_prec_radix,
	      d.interval_precision
	from  sybsystemprocs.dbo.spt_jdbc_datatype_info d,
	      sybsystemprocs.dbo.spt_datatype_info_ext e, systypes t
	where d.ss_dtype = t.type and t.usertype *= e.user_type
	     /* restrict results to 'real' datatypes, exclude float, date and time*/
	      and t.name not in ('nchar','nvarchar','sysname','timestamp','longsysname', 'float',
                         'datetimn','floatn','intn','moneyn', 'unichar',
                         'univarchar', 'daten', 'timen', 'date', 'time','uintn')
			  and t.usertype < 100    /* No user defined types */
			  
      open jdbc_datatype_info_cursor1

      fetch jdbc_datatype_info_cursor1 into
 	@type_name,
 	@data_type,
 	@precision,
 	@literal_prefix,
 	@literal_suffix,
 	@create_params,	
 	@nullable,
 	@case_sensitive,
 	@searchable,
 	@unsigned_attribute,
 	@fixed_prec_scale,
 	@auto_increment,
 	@local_type_name,
 	@minimum_scale,
 	@maximum_scale,
 	@sql_data_type,
	@sql_datetime_sub,
	@num_prec_radix,
	@interval_precision

	/** start insert the rows by looping thru the cursors */
        while (@@sqlstatus = 0)
        begin
		insert into #jdbc_datatype_info_cts values(
		/* TYPE_NAME */
		@type_name,
		@data_type,
		@precision,
		@literal_prefix,
		@literal_suffix,
		@create_params,	
		@nullable,
		@case_sensitive,
		@searchable,
		@unsigned_attribute,
		@fixed_prec_scale,
		@auto_increment,
		@local_type_name,
		@minimum_scale,
		@maximum_scale,
		@sql_data_type,
		@sql_datetime_sub,
		@num_prec_radix)

      fetch jdbc_datatype_info_cursor1 into
 	@type_name,
 	@data_type,
 	@precision,
 	@literal_prefix,
 	@literal_suffix,
 	@create_params,	
 	@nullable,
 	@case_sensitive,
 	@searchable,
 	@unsigned_attribute,
 	@fixed_prec_scale,
 	@auto_increment,
 	@local_type_name,
 	@minimum_scale,
 	@maximum_scale,
 	@sql_data_type,
	@sql_datetime_sub,
	@num_prec_radix,
	@interval_precision
	end	
	
      deallocate cursor jdbc_datatype_info_cursor1
      declare jdbc_datatype_info_cursor2 cursor for
	select  /* SQL Server user data types */
            case 
                when t.name = 'usmallint' then 'unsigned smallint'
                when t.name = 'uint' then 'unsigned int'
                when t.name = 'ubigint' then 'unsigned bigint'
            else
                t.name 
            end,
	      d.data_type,
	      isnull(d.data_precision, convert(int,t.length)),
	      d.literal_prefix,
	      d.literal_suffix,
	      e.create_params,
	      d.nullable,
	      d.case_sensitive,
	      d.searchable,
	      d.unsigned_attribute,
	      d.money,
	      d.auto_increment,
	      t.name,
	      d.minimum_scale,
	      d.maximum_scale,
	      d.sql_data_type,
	      d.sql_datetime_sub,
	      d.num_prec_radix,
	      d.interval_precision
	from  sybsystemprocs.dbo.spt_jdbc_datatype_info d,
	      sybsystemprocs.dbo.spt_datatype_info_ext e, systypes t
		where d.ss_dtype = t.type and t.usertype *= e.user_type
		/* Restrict to user defined types (value > 100)  and Sybase user defined 
		** types (listed)*/
	      and (t.name in ('nchar','nvarchar')
	      or t.usertype >= 100)      /* User defined types */

      open jdbc_datatype_info_cursor2

      fetch jdbc_datatype_info_cursor2 into
 	@type_name,
 	@data_type,
 	@precision,
 	@literal_prefix,
 	@literal_suffix,
 	@create_params,	
 	@nullable,
 	@case_sensitive,
 	@searchable,
 	@unsigned_attribute,
 	@fixed_prec_scale,
 	@auto_increment,
 	@local_type_name,
 	@minimum_scale,
 	@maximum_scale,
 	@sql_data_type,
	@sql_datetime_sub,
	@num_prec_radix,
	@interval_precision
	
	/** start insert the rows by looping thru the cursors */
        while (@@sqlstatus = 0)
        begin
		insert into #jdbc_datatype_info_cts values(
		/* TYPE_NAME */
		@type_name,
		@data_type,
		@precision,
		@literal_prefix,
		@literal_suffix,
		@create_params,	
		@nullable,
		@case_sensitive,
		@searchable,
		@unsigned_attribute,
		@fixed_prec_scale,
		@auto_increment,
		@local_type_name,
		@minimum_scale,
		@maximum_scale,
		@sql_data_type,
		@sql_datetime_sub,
		@num_prec_radix)

      fetch jdbc_datatype_info_cursor2 into
 	@type_name,
 	@data_type,
 	@precision,
 	@literal_prefix,
 	@literal_suffix,
 	@create_params,	
 	@nullable,
 	@case_sensitive,
 	@searchable,
 	@unsigned_attribute,
 	@fixed_prec_scale,
 	@auto_increment,
 	@local_type_name,
 	@minimum_scale,
 	@maximum_scale,
 	@sql_data_type,
	@sql_datetime_sub,
	@num_prec_radix,
	@interval_precision
	end	
	
      deallocate cursor jdbc_datatype_info_cursor2	
      declare jdbc_datatype_info_cursor3 cursor for
	select  /* ADD double precision which is floatn internally*/
	      'double precision',
	      8,
	      15,
	      d.literal_prefix,
	      d.literal_suffix,
	      e.create_params,
	      d.nullable,
	      d.case_sensitive,
	      d.searchable,
	      d.unsigned_attribute,
	      d.money,
	      d.auto_increment,
	      'double precision',
	      d.minimum_scale,      
	      d.maximum_scale,
	      d.sql_data_type,
	      d.sql_datetime_sub,
	      d.num_prec_radix,
	      d.interval_precision
		from  sybsystemprocs.dbo.spt_jdbc_datatype_info d,
		      sybsystemprocs.dbo.spt_datatype_info_ext e, systypes t
		where d.ss_dtype = t.type   and t.usertype *= e.user_type
		      and t.name = 'floatn' and t.usertype < 100 
      open jdbc_datatype_info_cursor3

      fetch jdbc_datatype_info_cursor3 into
 	@type_name,
 	@data_type,
 	@precision,
 	@literal_prefix,
 	@literal_suffix,
 	@create_params,	
 	@nullable,
 	@case_sensitive,
 	@searchable,
 	@unsigned_attribute,
 	@fixed_prec_scale,
 	@auto_increment,
 	@local_type_name,
 	@minimum_scale,
 	@maximum_scale,
 	@sql_data_type,
	@sql_datetime_sub,
	@num_prec_radix,
	@interval_precision
	
	/** start insert the rows by looping thru the cursors */
        while (@@sqlstatus = 0)
        begin
		insert into #jdbc_datatype_info_cts values(
		/* TYPE_NAME */
		@type_name,
		@data_type,
		@precision,
		@literal_prefix,
		@literal_suffix,
		@create_params,	
		@nullable,
		@case_sensitive,
		@searchable,
		@unsigned_attribute,
		@fixed_prec_scale,
		@auto_increment,
		@local_type_name,
		@minimum_scale,
		@maximum_scale,
		@sql_data_type,
		@sql_datetime_sub,
		@num_prec_radix)

      fetch jdbc_datatype_info_cursor3 into
 	@type_name,
 	@data_type,
 	@precision,
 	@literal_prefix,
 	@literal_suffix,
 	@create_params,	
 	@nullable,
 	@case_sensitive,
 	@searchable,
 	@unsigned_attribute,
 	@fixed_prec_scale,
 	@auto_increment,
 	@local_type_name,
 	@minimum_scale,
 	@maximum_scale,
 	@sql_data_type,
	@sql_datetime_sub,
	@num_prec_radix,
	@interval_precision
	end	
	
      deallocate cursor jdbc_datatype_info_cursor3	
      declare jdbc_datatype_info_cursor4 cursor for
	select  
	      'float',
	      8,
	      8,
	      d.literal_prefix,
	      d.literal_suffix,
	      e.create_params,
	      d.nullable,
	      d.case_sensitive,
	      d.searchable,
	      d.unsigned_attribute,
	      d.money,
	      d.auto_increment,
	      'float',
	      d.minimum_scale,      
	      d.maximum_scale,
	      d.sql_data_type,
	      d.sql_datetime_sub,
	      d.num_prec_radix,
	      d.interval_precision
	from  sybsystemprocs.dbo.spt_jdbc_datatype_info d,
	      sybsystemprocs.dbo.spt_datatype_info_ext e, systypes t
	where d.ss_dtype = t.type   and t.usertype *= e.user_type
	      and t.name = 'float' and t.usertype < 100  
	      
      open jdbc_datatype_info_cursor4

      fetch jdbc_datatype_info_cursor4 into
 	@type_name,
 	@data_type,
 	@precision,
 	@literal_prefix,
 	@literal_suffix,
 	@create_params,	
 	@nullable,
 	@case_sensitive,
 	@searchable,
 	@unsigned_attribute,
 	@fixed_prec_scale,
 	@auto_increment,
 	@local_type_name,
 	@minimum_scale,
 	@maximum_scale,
 	@sql_data_type,
	@sql_datetime_sub,
	@num_prec_radix,
	@interval_precision
	
	/** start insert the rows by looping thru the cursors */
        while (@@sqlstatus = 0)
        begin
		insert into #jdbc_datatype_info_cts values(
		/* TYPE_NAME */
		@type_name,
		@data_type,
		@precision,
		@literal_prefix,
		@literal_suffix,
		@create_params,	
		@nullable,
		@case_sensitive,
		@searchable,
		@unsigned_attribute,
		@fixed_prec_scale,
		@auto_increment,
		@local_type_name,
		@minimum_scale,
		@maximum_scale,
		@sql_data_type,
		@sql_datetime_sub,
		@num_prec_radix)

      fetch jdbc_datatype_info_cursor4 into
 	@type_name,
 	@data_type,
 	@precision,
 	@literal_prefix,
 	@literal_suffix,
 	@create_params,	
 	@nullable,
 	@case_sensitive,
 	@searchable,
 	@unsigned_attribute,
 	@fixed_prec_scale,
 	@auto_increment,
 	@local_type_name,
 	@minimum_scale,
 	@maximum_scale,
 	@sql_data_type,
	@sql_datetime_sub,
	@num_prec_radix,
	@interval_precision
	end	
	
      deallocate cursor jdbc_datatype_info_cursor4	
      declare jdbc_datatype_info_cursor5 cursor for
	select  /* Add date and time now. Special case because we want to use */
        /* d.sql_data_type for DATA_TYPE for these two types          */
            case 
                when t.name = 'usmallint' then 'unsigned smallint'
                when t.name = 'uint' then 'unsigned int'
                when t.name = 'ubigint' then 'unsigned bigint'
            else
                t.name 
            end,
      d.sql_data_type,
      isnull(d.data_precision, convert(int,t.length)),
      d.literal_prefix,
      d.literal_suffix,
      e.create_params,
      d.nullable,
      d.case_sensitive,
      d.searchable,
      d.unsigned_attribute,
      d.money,
      d.auto_increment,
      d.local_type_name,
      d.minimum_scale,
      d.maximum_scale,
      d.sql_data_type,
      d.sql_datetime_sub,
      d.num_prec_radix,
      d.interval_precision
	from  sybsystemprocs.dbo.spt_jdbc_datatype_info d,
	      sybsystemprocs.dbo.spt_datatype_info_ext e, systypes t
	where d.ss_dtype = t.type and t.usertype *= e.user_type
	     /* restrict results to date and time*/
	      and t.name in ('date', 'time')
	      and t.usertype < 100    /* No user defined types */      
      open jdbc_datatype_info_cursor5

      fetch jdbc_datatype_info_cursor5 into
 	@type_name,
 	@data_type,
 	@precision,
 	@literal_prefix,
 	@literal_suffix,
 	@create_params,	
 	@nullable,
 	@case_sensitive,
 	@searchable,
 	@unsigned_attribute,
 	@fixed_prec_scale,
 	@auto_increment,
 	@local_type_name,
 	@minimum_scale,
 	@maximum_scale,
 	@sql_data_type,
	@sql_datetime_sub,
	@num_prec_radix,
	@interval_precision
	
	/** start insert the rows by looping thru the cursors */
        while (@@sqlstatus = 0)
        begin
		insert into #jdbc_datatype_info_cts values(
		/* TYPE_NAME */
		@type_name,
		@data_type,
		@precision,
		@literal_prefix,
		@literal_suffix,
		@create_params,	
		@nullable,
		@case_sensitive,
		@searchable,
		@unsigned_attribute,
		@fixed_prec_scale,
		@auto_increment,
		@local_type_name,
		@minimum_scale,
		@maximum_scale,
		@sql_data_type,
		@sql_datetime_sub,
		@num_prec_radix)

      fetch jdbc_datatype_info_cursor5 into
 	@type_name,
 	@data_type,
 	@precision,
 	@literal_prefix,
 	@literal_suffix,
 	@create_params,	
 	@nullable,
 	@case_sensitive,
 	@searchable,
 	@unsigned_attribute,
 	@fixed_prec_scale,
 	@auto_increment,
 	@local_type_name,
 	@minimum_scale,
 	@maximum_scale,
 	@sql_data_type,
	@sql_datetime_sub,
	@num_prec_radix,
	@interval_precision
	end	
	
      deallocate cursor jdbc_datatype_info_cursor5	
	      
end /* first begin */

select * from #jdbc_datatype_info_cts order by DATA_TYPE, TYPE_NAME
drop table #jdbc_datatype_info_cts

if (@startedInTransaction = 1)
  rollback transaction jdbc_keep_temptables_from_tx

return(0) 
go
exec sp_procxmode 'sp_jdbc_datatype_info_cts', 'anymode'
go
grant execute on sp_jdbc_datatype_info_cts to public
go
dump transaction sybsystemprocs with truncate_only 
go

/*
**  End of sp_jdbc_datatype_info_cts
*/


/*
**  sp_jdbc_datatype_info
*/


/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects where name = 'sp_jdbc_datatype_info')
	begin
	drop procedure sp_jdbc_datatype_info
	end
go
/** SECTION END: CLEANUP **/

create procedure sp_jdbc_datatype_info
as

if @@trancount = 0
begin
		set chained off
end

set transaction isolation level 1

/* this will make sure that all rows are sent even if
** the client "set rowcount" is differect
*/

set rowcount 0

select  /* Real SQL Server data types */
	case
		when t.name = 'usmallint' then 'unsigned smallint'
		when t.name = 'uint' then 'unsigned int'
		when t.name = 'ubigint' then 'unsigned bigint'
	else
		t.name
	end as TYPE_NAME,
	  d.data_type as DATA_TYPE,
	  isnull(d.data_precision, convert(int,t.length)) as 'PRECISION',
	  d.literal_prefix as LITERAL_PREFIX,
	  d.literal_suffix as LITERAL_SUFFIX,
	  e.create_params as CREATE_PARAMS,
	  d.nullable as NULLABLE,
	  d.case_sensitive as CASE_SENSITIVE,
	  d.searchable as SEARCHABLE,
	  d.unsigned_attribute as UNSIGNED_ATTRIBUTE,
	  d.money as FIXED_PREC_SCALE,
	  d.auto_increment as AUTO_INCREMENT,
	  d.local_type_name as LOCAL_TYPE_NAME,
	  d.minimum_scale as MINIMUM_SCALE,
	  d.maximum_scale as MAXIMUM_SCALE,
	  d.sql_data_type as SQL_DATA_TYPE,
	  d.sql_datetime_sub as SQL_DATETIME_SUB,
	  d.num_prec_radix as NUM_PREC_RADIX,
	  d.interval_precision as INTERVAL_PRECISION
from  sybsystemprocs.dbo.spt_jdbc_datatype_info d,
	  sybsystemprocs.dbo.spt_datatype_info_ext e, systypes t
where d.ss_dtype = t.type and t.usertype *= e.user_type
	 /* restrict results to 'real' datatypes, exclude float, date and time*/
	  and t.name not in ('nchar','nvarchar','sysname','timestamp','longsysname', 'float',
						 'datetimn','floatn','intn','moneyn', 'unichar',
						 'univarchar', 'daten', 'timen', 'date', 'time','uintn')
	  and t.usertype < 100    /* No user defined types */
UNION
select  /* SQL Server user data types */
	case
		when t.name = 'usmallint' then 'unsigned smallint'
		when t.name = 'uint' then 'unsigned int'
		when t.name = 'ubigint' then 'unsigned bigint'
	else
		t.name
	end as TYPE_NAME,
	  d.data_type,
	  isnull(d.data_precision, convert(int,t.length)) as 'PRECISION',
	  d.literal_prefix, d.literal_suffix, e.create_params,      d.nullable,
	  d.case_sensitive, d.searchable,     d.unsigned_attribute, d.money,
	  d.auto_increment, t.name,           d.minimum_scale,      
	  d.maximum_scale,  d.sql_data_type,  d.sql_datetime_sub,
	  d.num_prec_radix, d.interval_precision
from  sybsystemprocs.dbo.spt_jdbc_datatype_info d,
	  sybsystemprocs.dbo.spt_datatype_info_ext e, systypes t
where d.ss_dtype = t.type and t.usertype *= e.user_type
/* Restrict to user defined types (value > 100)  and Sybase user defined 
** types (listed)*/
	  and (t.name in ('nchar','nvarchar')
	  or t.usertype >= 100)      /* User defined types */
UNION                                                  
select  /* ADD double precision which is floatn internally*/
	  'double precision',8,                15,
	  d.literal_prefix,  d.literal_suffix, e.create_params,      d.nullable,
	  d.case_sensitive,  d.searchable,     d.unsigned_attribute, d.money,
	  d.auto_increment, 'double precision',d.minimum_scale,      
	  d.maximum_scale,   d.sql_data_type,  d.sql_datetime_sub,
	  d.num_prec_radix,  d.interval_precision
from  sybsystemprocs.dbo.spt_jdbc_datatype_info d,
	  sybsystemprocs.dbo.spt_datatype_info_ext e, systypes t
where d.ss_dtype = t.type   and t.usertype *= e.user_type
	  and t.name = 'floatn' and t.usertype < 100 
UNION   /* ADD float now */
select  
	  'float',           8,                8,
	  d.literal_prefix,  d.literal_suffix, e.create_params,      d.nullable,
	  d.case_sensitive,  d.searchable,     d.unsigned_attribute, d.money,
	  d.auto_increment, 'float',           d.minimum_scale,      
	  d.maximum_scale,   d.sql_data_type,  d.sql_datetime_sub,
	  d.num_prec_radix,  d.interval_precision
from  sybsystemprocs.dbo.spt_jdbc_datatype_info d,
	  sybsystemprocs.dbo.spt_datatype_info_ext e, systypes t
where d.ss_dtype = t.type   and t.usertype *= e.user_type
	  and t.name = 'float' and t.usertype < 100 
UNION
select  /* Add date and time now. Special case because we want to use */
		/* d.sql_data_type for DATA_TYPE for these two types          */
	case
		when t.name = 'usmallint' then 'unsigned smallint'
		when t.name = 'uint' then 'unsigned int'
		when t.name = 'ubigint' then 'unsigned bigint'
	else
		t.name
	end as TYPE_NAME,
	  d.sql_data_type,
	  isnull(d.data_precision, convert(int,t.length)),
	  d.literal_prefix,
	  d.literal_suffix,
	  e.create_params,
	  d.nullable,
	  d.case_sensitive,
	  d.searchable,
	  d.unsigned_attribute,
	  d.money,
	  d.auto_increment,
	  d.local_type_name,
	  d.minimum_scale,
	  d.maximum_scale,
	  d.sql_data_type,
	  d.sql_datetime_sub,
	  d.num_prec_radix,
	  d.interval_precision
from  sybsystemprocs.dbo.spt_jdbc_datatype_info d,
	  sybsystemprocs.dbo.spt_datatype_info_ext e, systypes t
where d.ss_dtype = t.type and t.usertype *= e.user_type
	 /* restrict results to date and time*/
	  and t.name in ('date', 'time')
	  and t.usertype < 100    /* No user defined types */
order by DATA_TYPE, TYPE_NAME

return(0) 
go
exec sp_procxmode 'sp_jdbc_datatype_info', 'anymode'
go
grant execute on sp_jdbc_datatype_info to public
go
/* UNION Operation is done in tempdb? */
dump transaction tempdb with truncate_only 
go
dump transaction sybsystemprocs with truncate_only 
go

/*
**  End of sp_jdbc_datatype_info
*/


/*
**  sp_drv_column_default
**  Note: ASE 12.0 and earlier complain if this proc isn't created before
**        sp_jdbc_columns, because sp_jdbc_columns calls this proc. Therefore,
**        make sure this proc is created first at metadata install time. 
*/

/* 
** obtain a column's default value -- this is a utility proc called by
** sp_jdbc_columns
*/


/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects where name = 'sp_drv_column_default')
begin
		drop procedure sp_drv_column_default
end
go
/** SECTION END: CLEANUP **/

create procedure sp_drv_column_default
/* Don't delete the following line. It is the checkpoint for sed */
/* Server dependent stored procedure add here ADDPOINT_COL_DEFAULT */
     (@obj_id int, @default_value varchar (1024) output)
as
    declare @text_count              int
    declare @default_holder          varchar (255)
    declare @rownum                  int
    declare @create_default_starts   int
    declare @default_starts          int
    declare @actual_default_starts   int
    declare @as_starts               int
    declare @length                  int
    declare @check_case_one          int
    declare @check_last_char         int
    declare @linefeed_char           char (2)
    declare @last_char               char (2)

    /* make sure @default_value starts out as empty */
    select @default_value = null

    /* initialize @check_case_one to false (0) */
    select @check_case_one = 0

    /* initialize @check_last_char to false (0) */
    select @check_last_char = 0

    /* initialize the @linefeed_char variable to linefeed */
    select @linefeed_char = char (10)

    /* Find out how many rows there are in syscomments defining the 
       default. If there are none, then we return a null */
    select @text_count = count (*) from syscomments
        where id = @obj_id

    if @text_count = 0
    begin
        return 0
    end

    /* See if the object is hidden (SYSCOM_TEXT_HIDDEN will be set).
       If it is, best we can do is return null */
    if exists (select 1 from syscomments where (status & 1 = 1)
        and id = @obj_id)
    begin
        return 0
    end

    select @rownum = 1
    declare default_value_cursor cursor for
        select text from syscomments where id = @obj_id
        order by number, colid2, colid

    open default_value_cursor

    fetch default_value_cursor into @default_holder
 
    while (@@sqlstatus = 0)
    begin

        if @rownum = 1
        begin
            /* find the default value                                       
            **  Note that ASE stores default values in more than one way:    
            **    1. If a client declares the column default value in the     
            **       table definition, ASE will store the word DEFAULT (in    
            **       all caps) followed by the default value, exactly as the  
            **       user entered it (meaning it will include quotes, if the
            **       value was a string constant). This DEFAULT word will
            **       be in all caps even if the user did something like this:
            **           create table foo (col1 varchar (10) DeFaULT 'bar')
            **    2. If a client does sp_bindefault to bind a default to 
            **       a column, ASE will include the text of the create default
            **       command, as entered. So, if the client did the following:
            **           create DeFAULt foo aS 'bar'
            **       that is exactly what ASE will place in the text column
            **       of syscomments.
            **       In this case, too, we have to be careful because ASE
            **       will sometimes include a newline character 
            **       at the end of the create default statement. This
            **       can happen if a client uses C isql to type in the
            **       create default command (if it comes in through java, then
            **       the newline and null are not present).
            **  Because of this, we have to be careful when trying to parse out
            **  the default value. */

            select @length = char_length (@default_holder)
            select @create_default_starts =
                charindex ('create default', lower(@default_holder))
            select @as_starts = charindex(' as ', lower(@default_holder))
            select @default_starts = charindex ('DEFAULT', @default_holder)

            if (@create_default_starts != 0 and @as_starts != 0) 
            begin

                /* If we get here, then we likely have case (2) above.
                ** However, it's still possible that the client did something
                ** like this:
                **     create table foo (col1 varchar (20) default 
                **         'create default foo as bar')            
                ** The following if block accounts for that possibility  */

                if (@default_starts != 0 and
                  @default_starts < @create_default_starts)
                begin
                    select @check_case_one = 1
                end
                else
                begin
                    select @actual_default_starts = @as_starts + 4
                    select @check_last_char = 1

                    /* set @default_starts to 0 so we don't fall into the
                    ** next if block. This is important because we would
                    ** fall into the next if block if a client had used the
                    ** following sql:
                    **     CREATE DEFAULT foo as 'bar'               */
                    select @default_starts = 0
                end
            end

            if (@default_starts != 0 or @check_case_one != 0)
                /* If we get here, then we have case (1) above */
 
                select @actual_default_starts = @default_starts + 7
           
            /* The ltrim removes any left-side blanks, because ASE appears
            ** to insert several blanks between the word DEFAULT and the
            ** start of the default vale */
 
            select @default_holder = 
                ltrim(substring
                    (@default_holder, @actual_default_starts, @length))

        end

        select @default_value = @default_value + @default_holder
        select @rownum = @rownum + 1

        fetch default_value_cursor into @default_holder

    end /* while loop */

    close default_value_cursor

    /* trim off any right-side blanks */
    select @default_value = rtrim (@default_value)
 
    /* trim off the newline and null characters, if they're the last 
    ** two characters in what remains */
    if (@check_last_char = 1)
    begin

        select @length = char_length (@default_value)
        select @last_char = substring (@default_value, @length, 1)
        if (@last_char = @linefeed_char)
            select @default_value = substring (@default_value, 1, (@length - 1))
    end
   
    return 0


go
exec sp_procxmode 'sp_drv_column_default', 'anymode'
go
grant execute on sp_drv_column_default to public
go
dump transaction sybsystemprocs with truncate_only 
go

/* Don't delete the following line. It is the checkpoint for sed */
/* Server dependent stored procedure add here ADDPOINT_DRIVERS_COMMON */
if exists (select *
        from sysobjects
                where sysstat & 7 = 4
                        and name = 'sp_drv_typeinfo')
begin
        drop procedure sp_drv_typeinfo
end
go


create procedure sp_drv_typeinfo(@sstype int = 0)
as
        declare @curiso int
        select @curiso=@@isolation
        if @@isolation = 0
        begin
               
                set transaction isolation level 1
        end
        if @sstype = 0
            select literal_prefix, literal_suffix, case_sensitive, searchable, unsigned_attribute, num_prec_radix, ss_dtype from sybsystemprocs.dbo.spt_datatype_info
        else
            select literal_prefix, literal_suffix, case_sensitive, searchable, unsigned_attribute, num_prec_radix from sybsystemprocs.dbo.spt_datatype_info where ss_dtype = @sstype
        /*Not necessary, just for more clear logic */
        if @curiso = 0
        begin
                set transaction isolation level 0
        end


        return(0)
go

exec sp_procxmode 'sp_drv_typeinfo', 'anymode'
go
grant execute on sp_drv_typeinfo to public
go
dump tran master with truncate_only
go
dump transaction sybsystemprocs with truncate_only
go


if exists (select *
        from sysobjects
                where sysstat & 7 = 4
                        and name = 'sp_localtypename')
begin
        drop procedure sp_localtypename
end
go

create procedure sp_localtypename(@sstype int)
as

        declare @curiso int
        select @curiso=@@isolation
        if @@isolation = 0
        begin
               
                set transaction isolation level 1
        end
        select local_type_name from sybsystemprocs.dbo.spt_datatype_info where ss_dtype = @sstype

        if @curiso = 0
        begin
                set transaction isolation level 0
        end
        return(0)
go

exec sp_procxmode 'sp_localtypename', 'anymode'
go
grant execute on sp_localtypename to public
go
dump tran master with truncate_only
go
dump transaction sybsystemprocs with truncate_only
go


/*
**  sp_jdbc_columns
*/

use sybsystemprocs 
go

/* create a 1-off version of sp_jdbc_columns that has the additional
** columns required for ODBC 2.0 and more columns required by
** JDBC (from ODBC 3.0?).
*/

/** SECTION BEGIN: CLEANUP **/
if exists (select * from sysobjects where name = 'sp_jdbc_columns')
	begin
	drop procedure sp_jdbc_columns
	end
go
/** SECTION END: CLEANUP **/


/* This is the version for servers which support UNION */

CREATE PROCEDURE sp_jdbc_columns (
	@table_name         varchar(771),
	@table_owner        varchar(32 ) = null,
	@table_qualifier    varchar(32 ) = null,
	@column_name        varchar(771) = null,
	@version            int = null /* Conform to JDBC 4.0 spec if @version is not null */
)
AS
/* Don't delete the following line. It is the checkpoint for sed */
/* Server dependent stored procedure add here ad ADDPOINT_COLS */

    declare @o_uid              int
    declare @o_name             varchar (257)
    declare @d_data_type        smallint
    declare @d_aux              int
    declare @d_ss_dtype         tinyint
    declare @d_type_name        varchar (257)
    declare @d_data_precision   int
    declare @d_numeric_scale    smallint
    declare @d_numeric_radix    smallint
    declare @d_sql_data_type    smallint
    declare @c_name             varchar (257)
    declare @c_length           int
    declare @c_prec             tinyint
    declare @c_scale            tinyint
    declare @c_type             tinyint
    declare @c_colid            smallint
    declare @c_status           tinyint
    declare @c_cdefault         int
    declare @xtname             varchar (255)
    declare @ident              bit
  
    declare @msg              varchar(250)
    declare @full_table_name  varchar(1542)
    declare @table_id         int
    declare @char_bin_types   varchar(30)
    declare @uni_types        varchar (10)
    declare @column_default   varchar (1024)
    declare @startedInTransaction bit





    if (@@trancount = 0)
    begin
        set chained off
    end

    if (@@trancount > 0)
        select @startedInTransaction = 1
    else
        select @startedInTransaction = 0

    set transaction isolation level 1

    if (@startedInTransaction = 1)
        save transaction jdbc_keep_temptables_from_tx

    /* this will make sure that all rows are sent even if
    ** the client "set rowcount" is differect
    */

    set rowcount 0

    /* character and binary datatypes */
    select @char_bin_types =
        char(47)+char(39)+char(45)+char(37)+char(35)+char(34)

    /* unichar, univarchar and unitext datatypes */
    /* Note that the actual type numbers are 155 (unichar), 135 (univarchar)
       and 174 (unitext), but because of issues that can arise when a server
       has utf-8 as the default charset and a non-binary sort order, we need
       to create a character string that is valid in utf-8. Therefore we 
       apply an offset of 60 to move the characters to be valid utf-8 chars.
       The stored proc later does a similar calculation to utilize these
       values properly */
    select @uni_types =
        char(95)+char(75)+char(114)

    if @column_name is null select @column_name = '%'
    
    if @table_qualifier is not null
    begin
        if db_name() != @table_qualifier
        begin	/* 
            ** If qualifier doesn't match current database: 18039
            ** Table qualifier must be name of current database
            */
            exec sp_getmessage 18039, @msg output
            raiserror 18039 @msg
            return (1)
        end
    end
    
    if @table_name is null
    begin	/*	If table name not supplied, match all */
        select @table_name = '%'
    end

    if @table_owner is null
    begin       /* If unqualified table name */
        SELECT @full_table_name = @table_name
        select @table_owner = '%'
    end
    else
    begin       /* Qualified table name */
        SELECT @full_table_name = @table_owner + '.' + @table_name
    end

    /* create the temp table to hold our results */

    create table #jdbc_columns (
        TABLE_CAT         varchar (32) null,
        TABLE_SCHEM       varchar (32) null,
        TABLE_NAME        varchar (257) null,
        COLUMN_NAME       varchar (257) null,
        DATA_TYPE         smallint null,
        TYPE_NAME         varchar (255) null,
        COLUMN_SIZE       int null,
        BUFFER_LENGTH     int null,
        DECIMAL_DIGITS    int null,
        NUM_PREC_RADIX    int null,
        NULLABLE          int null,
        REMARKS           varchar (255) null,
        COLUMN_DEF        varchar (512) null,
        SQL_DATA_TYPE     int null,
        SQL_DATETIME_SUB  int null,
        CHAR_OCTET_LENGTH int null,
        ORDINAL_POSITION  int null,
        IS_NULLABLE       varchar (10) null,
        SCOPE_CATLOG      varchar(32) null,
        SCOPE_SCHEMA      varchar(32) null,
        SCOPE_TABLE       varchar(32) null,
        SOURCE_DATA_TYPE  smallint null,
        IS_AUTOINCREMENT  varchar(10) null)


 
    /* Decide if we're going to take the branch where we are getting
       information on one table (first branch), or more than one */

    /* Get Object ID */
    SELECT @table_id = object_id(@full_table_name)
       /* If the table name parameter is valid, get the information */
    if ((charindex('%',@full_table_name) = 0) and
        (charindex('_',@full_table_name) = 0)  and
        (@table_id != 0))

    begin 

      declare jdbc_columns_cursor1 cursor for
        SELECT 
               c.cdefault,
               c.colid,
               c.length,
               c.name,
               c.prec,
               c.scale,
               c.status,
               c.type,
               d.aux, 
               d.data_precision,
               d.data_type,
               d.numeric_radix,
               d.numeric_scale,
               d.sql_data_type,
               d.ss_dtype,
               t.name,
               o.name,
               o.uid,
               xtname,
               convert(bit, (c.status & 0x80)) 

          FROM
            syscolumns c,
            sysobjects o,
            sybsystemprocs.dbo.spt_jdbc_datatype_info d,
            sysxtypes x,
            systypes t
        WHERE
            o.id = @table_id
            AND o.id = c.id
            /*
            ** We use syscolumn.usertype instead of syscolumn.type
            ** to do join with systypes.usertype. This is because
            ** for a column which allows null, type stores its
            ** Server internal datatype whereas usertype still
            ** stores its user defintion datatype.  For an example,
            ** a column of type 'decimal NULL', its usertype = 26,
            ** representing decimal whereas its type = 106
            ** representing decimaln. nullable in the select list
            ** already tells user whether the column allows null.
            ** In the case of user defining datatype, this makes
            ** more sense for the user.
            */
            AND c.usertype = t.usertype
            AND t.type = d.ss_dtype
            and c.xtype *= x.xtid
            AND o.type != 'P'
            AND c.name like @column_name ESCAPE '\'
            AND d.ss_dtype IN (111, 109, 38, 110, 68)       /* Just *N types */
            AND c.usertype < 100

      open jdbc_columns_cursor1

      fetch jdbc_columns_cursor1 into
        @c_cdefault,
        @c_colid,
        @c_length,
        @c_name,
        @c_prec,
        @c_scale,
        @c_status,
        @c_type,
        @d_aux, 
        @d_data_precision,
        @d_data_type,
        @d_numeric_radix,
        @d_numeric_scale,
        @d_sql_data_type,
        @d_ss_dtype,
        @d_type_name,
        @o_name,
        @o_uid,
        @xtname,
        @ident 

        /* INTn, FLOATn, DATETIMEn and MONEYn types */

        while (@@sqlstatus = 0)
        begin

          exec sp_drv_column_default @c_cdefault, @column_default out
 
          INSERT INTO #jdbc_columns values (
            /* TABLE_CAT */
            DB_NAME(),

            /* TABLE_SCHEM */
            USER_NAME (@o_uid),

            /* TABLE_NAME */
            @o_name,

            /* COLUMN_NAME */
            @c_name,

            /* DATA_TYPE */
            @d_data_type+convert(smallint,
                        isnull(@d_aux,
                        ascii(substring('666AAA@@@CB??GG',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,1))
                        -60)),

            /* TYPE_NAME */
            case
                when @ident = 1 then
                        case 
                            when @d_type_name = 'usmallint' then 'unsigned smallint identity'
                            when @d_type_name = 'uint' then 'unsigned int identity'
                            when @d_type_name = 'ubigint' then 'unsigned bigint identity'
                            else
                                    isnull(@xtname, rtrim(substring(@d_type_name,
                                    1+isnull(@d_aux,
                                    ascii(substring('III<<<MMMI<<A<A',
                                    2*(@d_ss_dtype%35+1)+2-8/@c_length,
                                    1))-60), 255)))+' identity'
                            end
                when @d_type_name = 'usmallint' then 'unsigned smallint'
                when @d_type_name = 'uint' then 'unsigned int'
                when @d_type_name = 'ubigint' then 'unsigned bigint'
                else
                    isnull(@xtname, rtrim(substring(@d_type_name,
                    1+isnull(@d_aux,
                    ascii(substring('III<<<MMMI<<A<A',
                    2*(@d_ss_dtype%35+1)+2-8/@c_length,
                    1))-60), 255)))
            end,

            /* COLUMN_SIZE */
            isnull(convert(int, @c_prec),
                      isnull(convert(int, @d_data_precision),
                             convert(int, @c_length)))
                        +isnull(@d_aux, convert(int,
                        ascii(substring('???AAAFFFCKFOLS',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,1))-60)),

            /* BUFFER_LENGTH */
            isnull(convert(int, @c_prec),
                      isnull(convert(int, @d_data_precision),
                             convert(int, @c_length)))
                        +isnull(@d_aux, convert(int,
                        ascii(substring('???AAAFFFCKFOLS',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,1))-60)),

            /* DECIMAL_DIGITS */ 
            isnull(convert(smallint, @c_scale), 
                       convert(smallint, @d_numeric_scale)) +
                        convert(smallint, isnull(@d_aux,
                        ascii(substring('<<<<<<<<<<<<<<?',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,
                        1))-60)),

            /* NUM_PREC_RADIX */
            @d_numeric_radix,

            /* NULLABLE */
            /* set nullability from status flag */
            convert(smallint, convert(bit, @c_status&8)),

            /* REMARKS */
            convert(varchar(254),null),	/* Remarks are NULL */

            /* COLUMN_DEF */
            @column_default,

            /* SQL_DATA_TYPE */
            isnull(@d_sql_data_type,
                      @d_data_type+convert(smallint,
                      isnull(@d_aux,
                      ascii(substring('666AAA@@@CB??GG',
                      2*(@d_ss_dtype%35+1)+2-8/@c_length,1))
                      -60))),

            /* SQL_DATATIME_SUB */
            NULL,

            /* CHAR_OCTET_LENGTH */
            /*
            ** if the datatype is of type CHAR or BINARY
            ** then set char_octet_length to the same value
            ** assigned in the "prec" column.
            **
            ** The first part of the logic is:
            **
            **   if(c.type is in (155, 135, 47, 39, 45, 37, 35, 34))
            **       set char_octet_length = prec;
            **   else
            **       set char_octet_length = 0;
            */
                /*
                ** check if in the list
                ** if so, return a 1 and multiply it by the precision 
                ** if not, return a 0 and multiply it by the precision
                */
                convert(smallint, 
                    substring('0111111', 
                        charindex(char(@c_type), @char_bin_types) +
                        charindex(char(@c_type-60), @uni_types) + 1, 1)) *
                /* calculate the precision */
                isnull(convert(int, @c_prec),
                    isnull(convert(int, @d_data_precision),
                        convert(int,@c_length)))
                    +isnull(@d_aux, convert(int,
                        ascii(substring('???AAAFFFCKFOLS',
                            2*(@d_ss_dtype%35+1)+2-8/@c_length,1))-60)),
       
            /* ORDINAL_POSITION */
            @c_colid,

            /* IS_NULLABLE */
            rtrim(substring('NO YES', convert(smallint, 
                convert(bit, @c_status&8)*3)+1, 3)),
            null,null,null, null,/*SCOPE_CATLOG, SCOPE_SCHEMA , SCOPE_TABLE , SOURCE_DATA_TYPE 
                                   REF data type not supported*/
            /* IS_AUTOINCREMENT */
            rtrim(substring('NO YES', convert(smallint, convert(bit, @c_status&128)*3)+1, 3))

            ) /* close paren for values (*) */ 

          fetch jdbc_columns_cursor1 into
            @c_cdefault,
            @c_colid,
            @c_length,
            @c_name,
            @c_prec,
            @c_scale,
            @c_status,
            @c_type,
            @d_aux, 
            @d_data_precision,
            @d_data_type,
            @d_numeric_radix,
            @d_numeric_scale,
            @d_sql_data_type,
            @d_ss_dtype,
            @d_type_name,
            @o_name,
            @o_uid,
            @xtname,
            @ident
 
      end

      deallocate cursor jdbc_columns_cursor1

      declare jdbc_columns_cursor2 cursor for
        SELECT 
               c.cdefault,
               c.colid,
               c.length,
               c.name,
               c.prec,
               c.scale,
               c.status,
               c.type,
               d.aux, 
               d.data_precision,
               d.data_type,
               d.numeric_radix,
               d.numeric_scale,
               d.sql_data_type,
               d.ss_dtype,
               t.name,
               o.name,
               o.uid,
               xtname,
               convert(bit, (c.status & 0x80)) 
        FROM
            syscolumns c,
            sysobjects o,
            sybsystemprocs.dbo.spt_jdbc_datatype_info d,
	    sysxtypes x,
            systypes t
        WHERE
            o.id = @table_id
            AND o.id = c.id
            /*
            ** We use syscolumn.usertype instead of syscolumn.type
            ** to do join with systypes.usertype. This is because
            ** for a column which allows null, type stores its
            ** Server internal datatype whereas usertype still
            ** stores its user defintion datatype.  For an example,
            ** a column of type 'decimal NULL', its usertype = 26,
            ** representing decimal whereas its type = 106 
            ** representing decimaln. nullable in the select list
            ** already tells user whether the column allows null.
            ** In the case of user defining datatype, this makes
            ** more sense for the user.
            */
            AND c.usertype = t.usertype
            /*
            ** We need a equality join with 
            ** sybsystemprocs.dbo.spt_jdbc_datatype_info here so that
            ** there is only one qualified row returned from 
            ** sybsystemprocs.dbo.spt_jdbc_datatype_info, thus avoiding
            ** duplicates.
            */
            AND t.type = d.ss_dtype
	    and c.xtype *= x.xtid
            AND o.type != 'P'
            AND c.name like @column_name ESCAPE '\'
            AND (d.ss_dtype NOT IN (111, 109, 38, 110, 68) /* No *N types */
                OR c.usertype >= 100) /* User defined types */

      open jdbc_columns_cursor2

      fetch jdbc_columns_cursor2 into
          @c_cdefault,
          @c_colid,
          @c_length,
          @c_name,
          @c_prec,
          @c_scale,
          @c_status,
          @c_type,
          @d_aux, 
          @d_data_precision,
          @d_data_type,
          @d_numeric_radix,
          @d_numeric_scale,
          @d_sql_data_type,
          @d_ss_dtype,
          @d_type_name,
          @o_name,
          @o_uid,
          @xtname,
          @ident

      while (@@sqlstatus = 0)
      begin
 
          exec sp_drv_column_default @c_cdefault, @column_default out
          
          /* All other types including user data types */

          INSERT INTO #jdbc_columns values (
            
            /* TABLE_CAT */ 
            DB_NAME(),

            /* TABLE_SCHEM */
            USER_NAME(@o_uid),

            /* TABLE_NAME */
            @o_name,

            /*COLUMN_NAME*/
            @c_name,

            /* DATA_TYPE */
            @d_data_type+convert(smallint,
                        isnull(@d_aux,
                        ascii(substring('666AAA@@@CB??GG',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,1))
                        -60)),
 
            /* TYPE_NAME */
            case
                when @ident = 1 then
                        case 
                            when @d_type_name = 'usmallint' then 'unsigned smallint identity'
                            when @d_type_name = 'uint' then 'unsigned int identity'
                            when @d_type_name = 'ubigint' then 'unsigned bigint identity'
                            else
                            isnull(@xtname, rtrim(substring(@d_type_name,
                            1+isnull(@d_aux,
                            ascii(substring('III<<<MMMI<<A<A',
                            2*(@d_ss_dtype%35+1)+2-8/@c_length,
                            1))-60), 255)))+' identity'
                        end
                when @d_type_name = 'usmallint' then 'unsigned smallint'
                when @d_type_name = 'uint' then 'unsigned int'
                when @d_type_name = 'ubigint' then 'unsigned bigint'
                else
                        isnull(@xtname, rtrim(substring(@d_type_name,
                        1+isnull(@d_aux,
                        ascii(substring('III<<<MMMI<<A<A',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,
                        1))-60), 255)))
            end, 

            /* COLUMN_SIZE */  
            isnull(convert(int, @c_prec),
                      isnull(convert(int, @d_data_precision),
                        convert(int,@c_length / (1 +
                           (convert(smallint,
                            substring('011',
                        charindex(char(@c_type-60 ), @uni_types)+1, 1)))))))
                        +isnull(@d_aux, convert(int,
                        ascii(substring('???AAAFFFCKFOLS',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,1))-60)),
    
            /* BUFFER_LENGTH */ 
            isnull(convert(int, @c_prec),
                      isnull(convert(int, @d_data_precision),
                             convert(int, @c_length)))
                        +isnull(@d_aux, convert(int,
                        ascii(substring('???AAAFFFCKFOLS',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,1))-60)),

            /* DECIMAL_DIGITS */ 
            isnull(convert(smallint, @c_scale),
                       convert(smallint, @d_numeric_scale)) +
                        convert(smallint, isnull(@d_aux,
                        ascii(substring('<<<<<<<<<<<<<<?',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,
                        1))-60)),

            /* NUM_PREC_RADIX */
            @d_numeric_radix,

            /* NULLABLE */
            convert(smallint, convert(bit, @c_status&8)),

            /* REMARKS */
            convert(varchar(254),null),

            /* COLUMN_DEF */
            @column_default,

            /* SQL_DATA_TYPE */
            isnull(@d_sql_data_type,
                      @d_data_type+convert(smallint,
                      isnull(@d_aux,
                      ascii(substring('666AAA@@@CB??GG',
                      2*(@d_ss_dtype%35+1)+2-8/@c_length,1))
                      -60))),

            /* SQL_DATETIME_SUB */
            NULL,

            /* CHAR_OCTET_LENGTH */
            /*
            ** if the datatype is of type CHAR or BINARY
            ** then set char_octet_length to the same value
            ** assigned in the "prec" column.
            **
            ** The first part of the logic is:
            **
            **   if(c.type is in (155, 135, 47, 39, 45, 37, 35, 34))
            **       set char_octet_length = prec;
            **   else
            **       set char_octet_length = 0;
            */

                /*
                ** check if in the list
                ** if so, return a 1 and multiply it by the precision 
                ** if not, return a 0 and multiply it by the precision
                */
                convert(smallint, substring('0111111', 
                    charindex(char(@c_type), @char_bin_types) +
                    charindex(char(@c_type-60), @uni_types) + 1, 1)) *
                /* calculate the precision */
                isnull(convert(int, @c_prec),
                    isnull(convert(int, @d_data_precision),
                        convert(int,@c_length)))
                    +isnull(@d_aux, convert(int,
                        ascii(substring('???AAAFFFCKFOLS',
                            2*(@d_ss_dtype%35+1)+2-8/@c_length,1))-60)),

            /* ORDINAL_POSITION */
            @c_colid,

            /* IS_NULLABLE */
            rtrim(substring('NO YES', convert(smallint, 
                convert(bit, @c_status&8)*3)+1, 3)),
            null,null,null, null,/*SCOPE_CATLOG, SCOPE_SCHEMA , SCOPE_TABLE , SOURCE_DATA_TYPE 
                                   REF data type not supported*/
            /* IS_AUTOINCREMENT */
            rtrim(substring('NO YES', convert(smallint, convert(bit, @c_status&128)*3)+1, 3))

  
          ) /* close paren for values (*) */
 
          fetch jdbc_columns_cursor2 into
              @c_cdefault,
              @c_colid,
              @c_length,
              @c_name,
              @c_prec,
              @c_scale,
              @c_status,
              @c_type,
              @d_aux, 
              @d_data_precision,
              @d_data_type,
              @d_numeric_radix,
              @d_numeric_scale,
              @d_sql_data_type,
              @d_ss_dtype,
              @d_type_name,
              @o_name,
              @o_uid,
              @xtname,
              @ident

        end /* while loop */

      deallocate cursor jdbc_columns_cursor2

    end   /* if we have just one table */
 
    else
    begin

      /* We'll be iterating over more than one table */

      declare jdbc_columns_cursor3 cursor for
        select
               c.cdefault,
               c.colid,
               c.length,
               c.name,
               c.prec,
               c.scale,
               c.status,
               c.type,
               d.aux, 
               d.data_precision,
               d.data_type,
               d.numeric_radix,
               d.numeric_scale,
               d.sql_data_type,
               d.ss_dtype,
               t.name,
               o.name,
               o.uid,
               xtname,
               convert(bit, (c.status & 0x80))

          FROM
            syscolumns c,
            sysobjects o,
            sybsystemprocs.dbo.spt_jdbc_datatype_info d,
            sysxtypes x,
            systypes t

        WHERE
            o.name like @table_name ESCAPE '\'
            AND user_name(o.uid) like @table_owner ESCAPE '\'
            AND o.id = c.id
            /*
            ** We use syscolumn.usertype instead of syscolumn.type
            ** to do join with systypes.usertype. This is because
            ** for a column which allows null, type stores its
            ** Server internal datatype whereas usertype still
            ** stores its user defintion datatype.  For an example,
            ** a column of type 'decimal NULL', its usertype = 26,
            ** representing decimal whereas its type = 106
            ** representing decimaln. nullable in the select list
            ** already tells user whether the column allows null.
            ** In the case of user defining datatype, this makes
            ** more sense for the user.
            */
            AND c.usertype = t.usertype
            AND t.type = d.ss_dtype
            AND o.type != 'P'
            and c.xtype *= x.xtid
            AND c.name like @column_name ESCAPE '\'
            AND d.ss_dtype IN (111, 109, 38, 110, 68)       /* Just *N types */
            AND c.usertype < 100

        open jdbc_columns_cursor3

        fetch jdbc_columns_cursor3 into
          @c_cdefault,
          @c_colid,
          @c_length,
          @c_name,
          @c_prec,
          @c_scale,
          @c_status,
          @c_type,
          @d_aux, 
          @d_data_precision,
          @d_data_type,
          @d_numeric_radix,
          @d_numeric_scale,
          @d_sql_data_type,
          @d_ss_dtype,
          @d_type_name,
          @o_name,
          @o_uid,
          @xtname,
          @ident


        /* INTn, FLOATn, DATETIMEn and MONEYn types */

        while (@@sqlstatus = 0)
        begin

          exec sp_drv_column_default @c_cdefault, @column_default out
 
          INSERT INTO #jdbc_columns values (
            /* TABLE_CAT */
            DB_NAME(),

            /* TABLE_SCHEM */
            USER_NAME(@o_uid),

            /* TABLE_NAME */
            @o_name,

            /* COLUMN_NAME */
            @c_name,

            /* DATA_TYPE */
            @d_data_type+convert(smallint,
                        isnull(@d_aux,
                        ascii(substring('666AAA@@@CB??GG',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,1))
                        -60)),

            /* TYPE_NAME */
            case
                when @ident = 1 then
                        case 
                            when @d_type_name = 'usmallint' then 'unsigned smallint identity'
                            when @d_type_name = 'uint' then 'unsigned int identity'
                            when @d_type_name = 'ubigint' then 'unsigned bigint identity'
                            else
                            isnull(@xtname, rtrim(substring(@d_type_name,
                            1+isnull(@d_aux,
                            ascii(substring('III<<<MMMI<<A<A',
                            2*(@d_ss_dtype%35+1)+2-8/@c_length,
                            1))-60), 255)))+' identity'
                        end
                when @d_type_name = 'usmallint' then 'unsigned smallint'
                when @d_type_name = 'uint' then 'unsigned int'
                when @d_type_name = 'ubigint' then 'unsigned bigint'
                else
                        isnull(@xtname, rtrim(substring(@d_type_name,
                        1+isnull(@d_aux,
                        ascii(substring('III<<<MMMI<<A<A',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,
                        1))-60), 255)))
            end,

            /* COLUMN_SIZE */
            isnull(convert(int, @c_prec),
                      isnull(convert(int, @d_data_precision),
                             convert(int, @c_length)))
                        +isnull(@d_aux, convert(int,
                        ascii(substring('???AAAFFFCKFOLS',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,1))-60)),

            /* BUFFER_LENGTH */
            isnull(convert(int, @c_prec),
                      isnull(convert(int, @d_data_precision),
                             convert(int, @c_length)))
                        +isnull(@d_aux, convert(int,
                        ascii(substring('???AAAFFFCKFOLS',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,1))-60)),

            /* DECIMAL_DIGITS */ 
            isnull(convert(smallint, @c_scale), 
                       convert(smallint, @d_numeric_scale)) +
                        convert(smallint, isnull(@d_aux,
                        ascii(substring('<<<<<<<<<<<<<<?',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,
                        1))-60)),

            /* NUM_PREC_RADIX */
            @d_numeric_radix,

            /* NULLABLE */
            /* set nullability from status flag */
            convert(smallint, convert(bit, @c_status&8)),

            /* REMARKS */
            convert(varchar(254),null),	/* Remarks are NULL */

            /* COLUMN_DEF */
            @column_default,

            /* SQL_DATA_TYPE */
            isnull(@d_sql_data_type,
                      @d_data_type+convert(smallint,
                      isnull(@d_aux,
                      ascii(substring('666AAA@@@CB??GG',
                      2*(@d_ss_dtype%35+1)+2-8/@c_length,1))
                      -60))),

            /* SQL_DATATIME_SUB */
            NULL,

            /* CHAR_OCTET_LENGTH */
            /*
            ** if the datatype is of type CHAR or BINARY
            ** then set char_octet_length to the same value
            ** assigned in the "prec" column.
            **
            ** The first part of the logic is:
            **
            **   if(c.type is in (155, 135, 47, 39, 45, 37, 35, 34))
            **       set char_octet_length = prec;
            **   else
            **       set char_octet_length = 0;
            */

                /*
                ** check if in the list
                ** if so, return a 1 and multiply it by the precision 
                ** if not, return a 0 and multiply it by the precision
                */
                convert(smallint, 
                    substring('0111111', 
                        charindex(char(@c_type), @char_bin_types) +
                        charindex(char(@c_type-60), @uni_types) + 1, 1)) *
                /* calculate the precision */
                isnull(convert(int, @c_prec),
                    isnull(convert(int, @d_data_precision),
                        convert(int,@c_length)))
                    +isnull(@d_aux, convert(int,
                        ascii(substring('???AAAFFFCKFOLS',
                            2*(@d_ss_dtype%35+1)+2-8/@c_length,1))-60)),
       
            /* ORDINAL_POSITION */
            @c_colid,

            /* IS_NULLABLE */
            rtrim(substring('NO YES', convert(smallint, 
                convert(bit, @c_status&8)*3)+1, 3)),
            null,null,null, null,/*SCOPE_CATLOG, SCOPE_SCHEMA , SCOPE_TABLE , SOURCE_DATA_TYPE 
                                   REF data type not supported*/
            /* IS_AUTOINCREMENT */
            rtrim(substring('NO YES', convert(smallint, convert(bit, @c_status&128)*3)+1, 3))

          ) /* right paren for values (*) */
          
          fetch jdbc_columns_cursor3 into
              @c_cdefault,
              @c_colid,
              @c_length,
              @c_name,
              @c_prec,
              @c_scale,
              @c_status,
              @c_type,
              @d_aux, 
              @d_data_precision,
              @d_data_type,
              @d_numeric_radix,
              @d_numeric_scale,
              @d_sql_data_type,
              @d_ss_dtype,
              @d_type_name,
              @o_name,
              @o_uid,
              @xtname,
              @ident

        end /* while loop */

        deallocate cursor jdbc_columns_cursor3

        declare jdbc_columns_cursor4 cursor for
        SELECT 
               c.cdefault,
               c.colid,
               c.length,
               c.name,
               c.prec,
               c.scale,
               c.status,
               c.type,
               d.aux, 
               d.data_precision,
               d.data_type,
               d.numeric_radix,
               d.numeric_scale,
               d.sql_data_type,
               d.ss_dtype,
               t.name,
               o.name,
               o.uid,
               xtname,
               convert(bit, (c.status & 0x80))
        FROM
            syscolumns c,
            sysobjects o,
            sybsystemprocs.dbo.spt_jdbc_datatype_info d,
	    sysxtypes x,
            systypes t
        WHERE
            o.name like @table_name ESCAPE '\'
            AND user_name(o.uid) like @table_owner ESCAPE '\'
	    and c.xtype *= x.xtid 
            AND o.id = c.id
            /*
            ** We use syscolumn.usertype instead of syscolumn.type
            ** to do join with systypes.usertype. This is because
            ** for a column which allows null, type stores its
            ** Server internal datatype whereas usertype still
            ** stores its user defintion datatype.  For an example,
            ** a column of type 'decimal NULL', its usertype = 26,
            ** representing decimal whereas its type = 106 
            ** representing decimaln. nullable in the select list
            ** already tells user whether the column allows null.
            ** In the case of user defining datatype, this makes
            ** more sense for the user.
            */
            AND c.usertype = t.usertype
            /*
            ** We need a equality join with 
            ** sybsystemprocs.dbo.spt_jdbc_datatype_info here so that
            ** there is only one qualified row returned from 
            ** sybsystemprocs.dbo.spt_jdbc_datatype_info, thus avoiding
            ** duplicates.
            */
            AND t.type = d.ss_dtype
            AND o.type != 'P'
            AND c.name like @column_name ESCAPE '\'
            AND (d.ss_dtype NOT IN (111, 109, 38, 110, 68) /* No *N types */
                OR c.usertype >= 100) /* User defined types */

          open jdbc_columns_cursor4

          fetch jdbc_columns_cursor4 into
              @c_cdefault,
              @c_colid,
              @c_length,
              @c_name,
              @c_prec,
              @c_scale,
              @c_status,
              @c_type,
              @d_aux, 
              @d_data_precision,
              @d_data_type,
              @d_numeric_radix,
              @d_numeric_scale,
              @d_sql_data_type,
              @d_ss_dtype,
              @d_type_name,
              @o_name,
              @o_uid,
              @xtname,
              @ident


        while (@@sqlstatus = 0)
        begin
          exec sp_drv_column_default @c_cdefault, @column_default out

          /* All other types including user data types */
          INSERT INTO #jdbc_columns values (

            /* TABLE_CAT */ 
            DB_NAME(),

            /* TABLE_SCHEM */
            USER_NAME(@o_uid),

            /* TABLE_NAME */
            @o_name,

            /*COLUMN_NAME*/
            @c_name,

            /* DATA_TYPE */
            @d_data_type+convert(smallint,
                        isnull(@d_aux,
                        ascii(substring('666AAA@@@CB??GG',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,1))
                        -60)),
 
            /* TYPE_NAME */
            case
                when @ident = 1 then
                        case 
                            when @d_type_name = 'usmallint' then 'unsigned smallint identity'
                            when @d_type_name = 'uint' then 'unsigned int identity'
                            when @d_type_name = 'ubigint' then 'unsigned bigint identity'
                            else
                            isnull(@xtname, rtrim(substring(@d_type_name,
                            1+isnull(@d_aux,
                            ascii(substring('III<<<MMMI<<A<A',
                            2*(@d_ss_dtype%35+1)+2-8/@c_length,
                            1))-60), 255)))+' identity'
                        end
                when @d_type_name = 'usmallint' then 'unsigned smallint'
                when @d_type_name = 'uint' then 'unsigned int'
                when @d_type_name = 'ubigint' then 'unsigned bigint'
                else
                        isnull(@xtname, rtrim(substring(@d_type_name,
                        1+isnull(@d_aux,
                        ascii(substring('III<<<MMMI<<A<A',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,
                        1))-60), 255)))
            end, 

            /* COLUMN_SIZE */  
            isnull(convert(int, @c_prec),
                      isnull(convert(int, @d_data_precision),
                        convert(int,@c_length / (1 +
                           (convert(smallint,
                            substring('011',
                        charindex(char(@c_type-60), @uni_types)+1, 1)))))))
                        +isnull(@d_aux, convert(int,
                        ascii(substring('???AAAFFFCKFOLS',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,1))-60)),
    
            /* BUFFER_LENGTH */ 
            isnull(convert(int, @c_prec),
                      isnull(convert(int, @d_data_precision),
                             convert(int, @c_length)))
                        +isnull(@d_aux, convert(int,
                        ascii(substring('???AAAFFFCKFOLS',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,1))-60)),

            /* DECIMAL_DIGITS */ 
            isnull(convert(smallint, @c_scale),
                       convert(smallint, @d_numeric_scale)) +
                        convert(smallint, isnull(@d_aux,
                        ascii(substring('<<<<<<<<<<<<<<?',
                        2*(@d_ss_dtype%35+1)+2-8/@c_length,
                        1))-60)),

            /* NUM_PREC_RADIX */
            @d_numeric_radix,

            /* NULLABLE */
            convert(smallint, convert(bit, @c_status&8)),

            /* REMARKS */
            convert(varchar(254),null),

            /* COLUMN_DEF */
            @column_default,

            /* SQL_DATA_TYPE */
            isnull(@d_sql_data_type,
                      @d_data_type+convert(smallint,
                      isnull(@d_aux,
                      ascii(substring('666AAA@@@CB??GG',
                      2*(@d_ss_dtype%35+1)+2-8/@c_length,1))
                      -60))),

            /* SQL_DATETIME_SUB */
            NULL,

            /* CHAR_OCTET_LENGTH */
            /*
            ** if the datatype is of type CHAR or BINARY
            ** then set char_octet_length to the same value
            ** assigned in the "prec" column.
            **
            ** The first part of the logic is:
            **
            **   if(c.type is in (155, 135, 47, 39, 45, 37, 35, 34))
            **       set char_octet_length = prec;
            **   else
            **       set char_octet_length = 0;
            */

                /*
                ** check if in the list
                ** if so, return a 1 and multiply it by the precision 
                ** if not, return a 0 and multiply it by the precision
                */
                convert(smallint, substring('0111111', 
                    charindex(char(@c_type), @char_bin_types) +
                    charindex(char(@c_type-60), @uni_types) + 1, 1)) *
                /* calculate the precision */
                isnull(convert(int, @c_prec),
                    isnull(convert(int, @d_data_precision),
                        convert(int,@c_length)))
                    +isnull(@d_aux, convert(int,
                        ascii(substring('???AAAFFFCKFOLS',
                            2*(@d_ss_dtype%35+1)+2-8/@c_length,1))-60)),

            /* ORDINAL_POSITION */
            @c_colid,

            /* IS_NULLABLE */
            rtrim(substring('NO YES', convert(smallint, convert(bit, @c_status&8)*3)+1, 3)),
            null,null,null, null,/*SCOPE_CATLOG, SCOPE_SCHEMA , SCOPE_TABLE , SOURCE_DATA_TYPE 
                                   REF data type not supported*/
            /* IS_AUTOINCREMENT */
            rtrim(substring('NO YES', convert(smallint, convert(bit, @c_status&128)*3)+1, 3))
  
          ) /* close paren for values (*) */

          fetch jdbc_columns_cursor4 into
              @c_cdefault,
              @c_colid,
              @c_length,
              @c_name,
              @c_prec,
              @c_scale,
              @c_status,
              @c_type,
              @d_aux, 
              @d_data_precision,
              @d_data_type,
              @d_numeric_radix,
              @d_numeric_scale,
              @d_sql_data_type,
              @d_ss_dtype,
              @d_type_name,
              @o_name,
              @o_uid,
              @xtname,
              @ident

         end /* while loop */ 
         
         deallocate cursor jdbc_columns_cursor4

    end           
    if @version is not null
    begin
        SELECT * FROM #jdbc_columns
            ORDER BY TABLE_CAT, TABLE_SCHEM, TABLE_NAME, ORDINAL_POSITION
    end
    else
        SELECT TABLE_CAT        
            ,TABLE_SCHEM      
            ,TABLE_NAME       
            ,COLUMN_NAME      
            ,DATA_TYPE        
            ,TYPE_NAME        
            ,COLUMN_SIZE      
            ,BUFFER_LENGTH    
            ,DECIMAL_DIGITS   
            ,NUM_PREC_RADIX   
            ,NULLABLE         
            ,REMARKS          
            ,COLUMN_DEF       
            ,SQL_DATA_TYPE    
            ,SQL_DATETIME_SUB 
            ,CHAR_OCTET_LENGTH
            ,ORDINAL_POSITION 
            ,IS_NULLABLE
            ,SCOPE_CATLOG
            ,SCOPE_SCHEMA
            ,SCOPE_TABLE
            ,SOURCE_DATA_TYPE
        FROM #jdbc_columns
        ORDER BY TABLE_SCHEM, TABLE_NAME, ORDINAL_POSITION

    drop table #jdbc_columns

    if (@startedInTransaction = 1)
        rollback transaction jdbc_keep_temptables_from_tx 
 
    return(0)


go
exec sp_procxmode 'sp_jdbc_columns', 'anymode'
go
grant execute on sp_jdbc_columns to public
go
dump transaction sybsystemprocs with truncate_only 
go

/*
**  End of sp_jdbc_columns
*/


/*
** sp_jdbc_getclientinfoprops
*/

/** SECTION BEGIN: CLEANUP **/
if exists (select * from sysobjects where name = 'sp_jdbc_getclientinfoprops')
begin
	drop procedure sp_jdbc_getclientinfoprops
end
go
/** SECTION END: CLEANUP **/

create procedure sp_jdbc_getclientinfoprops
as
select 'ApplicationName' as Name, 30 as MAX_LEN, '' as DEFAULT_VALUE, 
'The name of the application currently utilizing the connection. It appears in the sysprocesses table under column clientapplname.' 
as DESCRIPTION 
union 
select 'ClientHostname' as Name, 30 as MAX_LEN, '' as DEFAULT_VALUE,
'The hostname of the computer the application using the connection is running on. It appears in the sysprocesses table under column clienthostname.' 
as DESCRIPTION
union
select 'ClientUser' as Name, 30 as MAX_LEN, '' as DEFAULT_VALUE,
'The name of the user that the application using the connection is performing work for. It appears in the sysprocesses table under column clientname.' 
as DESCRIPTION 
go


/*
** sp_jdbc_set_client_info
*/
/** SECTION BEGIN: CLEANUP **/
if exists (select * from sysobjects where name = 'sp_jdbc_set_client_info')
begin
	drop procedure sp_jdbc_set_client_info
end
go
/** SECTION END: CLEANUP **/

create procedure sp_jdbc_set_client_info (
                    @prop_name varchar(15),
                    @value varchar(30) )
as
     if @prop_name = 'clientname'
         set clientname @value
     else if @prop_name = 'clienthostname'
         set clienthostname @value
     else
         set clientapplname @value
go
/*
** sp_jdbc_set_client_info
*/

/*
**  sp_jdbc_tables
*/

/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects where name = 'sp_jdbc_tables')
begin
	drop procedure sp_jdbc_tables
end
go
/** SECTION END: CLEANUP **/

create procedure sp_jdbc_tables
	@table_name       varchar(771)  = null,
	@table_owner      varchar(32 )  = null,
	@table_qualifier  varchar(32 )  = null,
	@table_type       varchar(100) = null
as
	declare @msg varchar(90)
	declare @searchstr varchar(255)

	if @@trancount = 0
	begin
		set chained off
	end

	set transaction isolation level 1

	/* this will make sure that all rows are sent even if
	** the client "set rowcount" is differect
	*/

	set rowcount 0



	/* temp table */
   /* Adding tempdb check here depending on the ASE version ADDTEMPDB */

   if (@table_name like '#%' and db_name() != db_name(tempdb_id()))
	begin
		/*
		** Can return data about temp. tables only in tempdb
		*/
		exec sp_getmessage 17676, @msg out
		raiserror 17676 @msg
		return(1)
	end
		if @table_qualifier is not null
		begin
			if db_name() != @table_qualifier
			begin
			exec sp_getmessage 18039, @msg out
			raiserror 18039 @msg
			return 1
		end
	end

	if @table_name is null select @table_name = '%'
	if @table_owner is null select @table_owner = '%'

	select @searchstr = ''
	if (patindex('%''SYSTEM%',upper(@table_type)) > 0)
		select @searchstr = @searchstr + 'S'

	if (patindex('%''TABLE''%',upper(@table_type)) > 0)
		select @searchstr = @searchstr +'U'

	if (patindex('%''VIEW''%',upper(@table_type)) > 0) 
		select @searchstr = @searchstr +'V' 

	if @table_type is null 
		select @searchstr = 'SUV'
	if ((@table_type is not null) and (@searchstr=''))
	begin
		exec sp_getmessage 17301, @msg output
		raiserror 17301 @msg, @table_type
		return(3)
	end

	/*
	** Just return an empty result set with properly named columns
	** if (select count(*) from sysobjects where user_name(uid) like @table_owner
	**    	            and name like @table_name
	** 		    and charindex(substring(type,1,1),@searchstr)! = 0) = 0 
	** begin
	** 	exec sp_getmessage 17674, @msg output
	** 	raiserror 17674 @msg
	** 	return(1)
	** end
	*/

	select
		TABLE_CAT =  rtrim(db_name()),
		TABLE_SCHEM= rtrim(user_name(uid)),
		TABLE_NAME = rtrim(name),
		rtrim(substring('SYSTEM TABLE            TABLE       VIEW       ',
		(ascii(type)-83)*12+1,12)) as TABLE_TYPE,
		REMARKS=     convert(varchar(254),null)
	from sysobjects 
	where name like @table_name ESCAPE '\'
		and user_name(uid) like @table_owner ESCAPE '\'
		and charindex(substring(type,1,1),@searchstr)! = 0
	order by TABLE_TYPE, TABLE_SCHEM, TABLE_NAME
go
exec sp_procxmode 'sp_jdbc_tables', 'anymode'
go
grant execute on sp_jdbc_tables to public
go
/* rtrim, ascii, and other built-ins use tempdb for evaluation? */
dump transaction tempdb with truncate_only 
go
dump transaction sybsystemprocs with truncate_only 
go

/*
**  End of sp_jdbc_tables
*/


/*
**  spt_jdbc_table_types
*/


/** SECTION BEGIN: CLEANUP **/
use master
go

if (exists (select * from sysobjects
				where name = 'spt_jdbc_table_types'))
	drop table spt_jdbc_table_types
go
/** SECTION END: CLEANUP **/


create table spt_jdbc_table_types (TABLE_TYPE char(15))
go
	insert into spt_jdbc_table_types values('TABLE')
	insert into spt_jdbc_table_types values('SYSTEM TABLE')
	insert into spt_jdbc_table_types values('VIEW')
go

commit
go

grant select on spt_jdbc_table_types to public
go

/*
**  End of spt_jdbc_table_types
*/


/*
**  spt_mda
*/


/** SECTION BEGIN: CLEANUP **/
use master
go

if exists (select * from sysobjects where name = 'spt_mda')
begin
	drop table spt_mda
end
go
/** SECTION END: CLEANUP **/

/*
** querytype: 1 == RPC, 2 == LANGUAGE, 3 == NOT_SUPPORTED,
**            4 == LITERAL (boolean), 5 == LITERAL (integer),
**            6 == LITERAL (string), 7 == LITERAL (string, not tokenizable)
**
** note: querytypes 4 through 6 were added in version level 4 
**       of the metadata access.
** note: querytype 7 was added in version level 5 of the metadata access
**	 to fix 168844
** note: sp_mda version does NOT refer to the jConnect version!!
*/
create table spt_mda (mdinfo varchar(255), querytype tinyint, 
	query varchar(255) null, mdaver_start tinyint, mdaver_end tinyint, 
	srvver_start int, srvver_end int)
go

create unique nonclustered index spt_mda_ind 
	on spt_mda (mdinfo, mdaver_end, srvver_end)
go

grant select on spt_mda to public
go

insert spt_mda values ('CLASSFORNAME', 1, 'sp_jdbc_class_for_name(?)', 1, 9, 12000, -1)
insert spt_mda values ('JARFORCLASS', 1, 'sp_jdbc_jar_for_class(?)', 1, 9, 12000, -1)
insert spt_mda values ('JARBYNAME', 1, 'sp_jdbc_jar_by_name(?)', 1, 9, 12000, -1)
insert spt_mda values ('CLASSESINJAR', 1, 'sp_jdbc_classes_in_jar(?)', 1, 9, 12000, -1)
insert spt_mda values ('CANRETURNJARS', 4, '0', 4, 9, 0, 11950)
insert spt_mda values ('CANRETURNJARS', 2, 'select 0', 1, 3, 0, 11950)
insert spt_mda values ('CANRETURNJARS', 4, '1', 4, 9, 12000, -1)
insert spt_mda values ('CANRETURNJARS', 2, 'select 1', 1, 3, 12000, -1)

insert spt_mda values ('GET_CLIENT_INFO', 2, 'select clientapplname, clientname, clienthostname from master.dbo.sysprocesses where spid=@@spid', 1, 9, 0, -1)
insert spt_mda values ('SET_CLIENT_INFO', 1, 'sp_jdbc_set_client_info(?,?) ', 1, 9, 0, -1)
insert spt_mda values ('GETCLIENTINFOPROPERTIES', 1, 'sp_jdbc_getclientinfoprops', 1, 9, 0, -1)
insert spt_mda values ('FUNCTIONCALL', 1, 'sp_jdbc_function_escapes', 1, 9, 0, -1)
insert spt_mda values ('TYPEINFO', 1, 'sp_jdbc_datatype_info', 1, 9, 0, -1)
insert spt_mda values ('TYPEINFO_CTS', 1, 'sp_jdbc_datatype_info_cts', 1, 9, 0, -1)
insert spt_mda values ('TABLES', 1, 'sp_jdbc_tables(?,?,?,?)', 1, 9, 0, -1)
insert spt_mda values ('COLUMNS', 1, 'sp_jdbc_columns(?,?,?,?)', 1, 7, 0, -1)
insert spt_mda values ('COLUMNS', 1, 'sp_jdbc_columns(?,?,?,?,4)', 8, 8, 0, -1)
insert spt_mda values ('COLUMNS', 1, 'sp_jdbc_columns(?,?,?,?,?)', 9, 9, 0, -1)
insert spt_mda values ('BULK_INSERT', 1, 'sp_drv_bcpmetadata(?,?,?)', 1, 9, 0, -1)
insert spt_mda values ('SET_LOGBULKCOPY_ON', 2, 'set logbulkcopy on', 9,  9, 0, -1)
insert spt_mda values ('IS_LOGGED_BCP_SUPPORTED', 2, 'select 1 from spt_values where name = ''logbulkcopy''', 9, 9, 0, -1 )
insert spt_mda values ('IMPORTEDKEYS', 1, 'sp_jdbc_importkey(?,?,?)', 1, 9, 0, -1)
insert spt_mda values ('EXPORTEDKEYS', 1, 'sp_jdbc_exportkey(?,?,?)', 1, 9, 0, -1)
insert spt_mda values ('PRIMARYKEYS', 1, 'sp_jdbc_primarykey(?,?,?)', 1, 9, 0, -1)
insert spt_mda values ('PRODUCTNAME', 2, 'select substring (@@version, 1, charindex(''/'',@@version)-1)', 1, 9, 0, -1) 
insert spt_mda values ('GET_IDENTITY', 6, 'select @@identity', 1, 9, 0, -1)
insert spt_mda values ('ISREADONLY', 2, 'select 0', 1, 3, 0, -1)
insert spt_mda values ('ISREADONLY', 4, '0', 4, 9, 0, -1)
insert spt_mda values ('ALLPROCSCALLABLE', 2, 'select 0', 1, 3, 0, -1)
insert spt_mda values ('ALLPROCSCALLABLE', 4, '0', 4, 9, 0, -1)
insert spt_mda values ('ALLTABLESSELECTABLE', 2, 'select 0', 1, 3, 0, -1)
insert spt_mda values ('ALLTABLESSELECTABLE', 4, '0', 4, 9, 0, -1)
insert spt_mda values ('COLUMNALIASING', 2, 'select 1', 1, 3, 0, -1)
insert spt_mda values ('COLUMNALIASING', 4, '1', 4, 9, 0, -1)
insert spt_mda values ('IDENTIFIERQUOTE', 2, 'select ''"''', 1, 3, 0, -1)
insert spt_mda values ('IDENTIFIERQUOTE', 6, '"', 4, 9, 0, -1)
insert spt_mda values ('ALTERTABLESUPPORT', 2, 'select 1, 1', 1, 3, 0, -1)
insert spt_mda values ('ALTERTABLESUPPORT', 4, '1, 1', 4, 9, 0, -1)
insert spt_mda values ('CONNECTCONFIG', 2, 'set quoted_identifier on set textsize 2147483647 ', 1, 9, 0, -1)
insert spt_mda values ('CONVERTSUPPORT', 2, 'select 1', 1, 3, 0, -1)
insert spt_mda values ('CONVERTSUPPORT', 4, '1', 4, 9, 0, -1)
insert spt_mda values ('CONVERTMAP', 1, 'sp_jdbc_convert_datatype(?,?)', 1, 9, 0, -1)
insert spt_mda values ('LIKEESCAPECLAUSE', 2, 'select 1', 1, 3, 0, -1)
insert spt_mda values ('LIKEESCAPECLAUSE', 4, '1', 4, 9, 0, -1)
insert spt_mda values ('MULTIPLERESULTSETS', 2, 'select 1', 1, 3, 0, -1)
insert spt_mda values ('MULTIPLERESULTSETS', 4, '1', 4, 9, 0, -1)
insert spt_mda values ('MULTIPLETRANSACTIONS', 2, 'select 1', 1, 3, 0, -1)
insert spt_mda values ('MULTIPLETRANSACTIONS', 4, '1', 4, 9, 0, -1)
insert spt_mda values ('NONNULLABLECOLUMNS', 2, 'select 1', 1, 3, 0, -1) 
insert spt_mda values ('NONNULLABLECOLUMNS', 4, '1', 4, 9, 0, -1) 
insert spt_mda values ('POSITIONEDDELETE', 2, 'select 1', 1, 3, 0, -1)
insert spt_mda values ('POSITIONEDDELETE', 4, '1', 4, 9, 0, -1)
insert spt_mda values ('POSITIONEDUPDATE', 2, 'select 1', 1, 3, 0, -1)
insert spt_mda values ('POSITIONEDUPDATE', 4, '1', 4, 9, 0, -1)
insert spt_mda values ('STOREDPROCEDURES', 2, 'select 1', 1, 3, 0, -1)
insert spt_mda values ('STOREDPROCEDURES', 4, '1', 4, 9, 0, -1)
insert spt_mda values ('PROCEDURES', 1, 'sp_jdbc_stored_procedures(?,?,?)', 1, 7, 0, -1)
insert spt_mda values ('PROCEDURES', 1, 'sp_jdbc_stored_procedures(?,?,?,4)', 8, 8, 0, -1)
insert spt_mda values ('PROCEDURES', 1, 'sp_jdbc_stored_procedures(?,?,?,?)', 9, 9, 0, -1)
insert spt_mda values ('FUNCTIONS', 1, 'sp_jdbc_stored_procedures(?,?,?,4,1)', 8, 8, 0, -1)
insert spt_mda values ('FUNCTIONS', 1, 'sp_jdbc_stored_procedures(?,?,?,?,?)', 9, 9, 0, -1)
insert spt_mda values ('SELECTFORUPDATE', 2, 'select 1', 1, 3, 0, -1) 
insert spt_mda values ('SELECTFORUPDATE', 4, '1', 4, 9, 0, -1) 
insert spt_mda values ('CURSORTRANSACTIONS', 2, 'select 1, 1', 1, 3, 0, -1)
insert spt_mda values ('CURSORTRANSACTIONS', 4, '1, 1', 4, 9, 0, -1)
insert spt_mda values ('STATEMENTTRANSACTIONS', 2, 'select 1, 1', 1, 3, 0, -1)
insert spt_mda values ('STATEMENTTRANSACTIONS', 4, '1, 1', 4, 9, 0, -1)
insert spt_mda values ('TRANSACTIONSUPPORT', 2, 'select 1', 1, 3, 0, -1)
insert spt_mda values ('TRANSACTIONSUPPORT', 4, '1', 4, 9, 0, -1)
insert spt_mda values ('SAVEPOINTSUPPORT', 2, 'select 1', 1, 3, 0, -1)
insert spt_mda values ('SAVEPOINTSUPPORT', 4, '1', 4, 9, 0, -1)
insert spt_mda values ('JDBCMAJORVERSION', 5, '4', 8, 9, 0, -1)
insert spt_mda values ('JDBCMAJORVERSION', 5, '3', 4, 7, 0, -1)
insert spt_mda values ('JDBCMINORVERSION', 5, '0', 4, 9, 0, -1)

/* 
 *Set this to 1 if 'exec <dbname>..<storedProcName>' is allowed
 */
insert spt_mda values ('PREPEND_DB_NAME', 2, 'select 1', 1, 3, 0, -1) 
insert spt_mda values ('PREPEND_DB_NAME', 5, '1', 4, 9, 0, -1) 

-- note - transaction levels here match Connection.TRANSACTION
insert spt_mda values ('TRANSACTIONLEVELS', 1, 'sp_jdbc_getisolationlevels',1 ,9, 0, -1) 
insert spt_mda values ('TRANSACTIONLEVELDEFAULT', 2, 'select 2', 1, 3, 0, -1)
insert spt_mda values ('TRANSACTIONLEVELDEFAULT', 5, '2', 4, 9, 0, -1)
insert spt_mda values ('SET_ISOLATION', 2, 'set transaction isolation level ', 1, 9, 0, -1)
insert spt_mda values ('GET_ISOLATION', 2, 'select @@isolation ', 1, 9, 0, -1)
insert spt_mda values ('SET_ROWCOUNT', 2, 'set rowcount ?', 1, 9, 0, -1)
insert spt_mda values ('GET_AUTOCOMMIT', 2, 'select @@tranchained ', 1, 9, 0, -1)
insert spt_mda values ('SET_AUTOCOMMIT_ON', 2, 'set CHAINED off', 1, 9, 0, -1)
insert spt_mda values ('SET_AUTOCOMMIT_OFF', 2, 'set CHAINED on', 1, 9, 0, -1)
insert spt_mda values ('BEGIN_TRAN', 2, 'if @@trancount < 1 begin tran', 1, 9, 0, -1)
insert spt_mda values ('SAVEPOINT', 2, 'save tran', 1, 9, 0, -1)
insert spt_mda values ('ROLL_TO_SAVEPOINT', 2, 'rollback tran', 1, 9, 0, -1)
insert spt_mda values ('USERNAME', 2, 'select user_name()', 1, 9, 0, -1)
insert spt_mda values ('SET_READONLY_TRUE', 3, '', 1, 9, 0, -1)
insert spt_mda values ('SET_READONLY_FALSE', 3, '', 1, 9, 0, -1)
insert spt_mda values ('SET_CATALOG', 2, 'use ?', 1, 9, 0, -1)
insert spt_mda values ('GET_CATALOG', 2, 'select db_name()', 1, 9, 0, -1)
insert spt_mda values ('NULLSORTING', 2, 'select 0, 1, 0, 0', 1, 3, 0, -1)
insert spt_mda values ('NULLSORTING', 4, '0, 1, 0, 0', 4, 9, 0, -1)
insert spt_mda values ('PRODUCTVERSION', 2, 'select @@version', 1, 9, 0, -1)
insert spt_mda values ('FILEUSAGE', 2, 'select 0, 0', 1, 3, 0, -1)
insert spt_mda values ('FILEUSAGE', 4, '0, 0', 4, 9, 0, -1)
if ('a'='A')  	/* Case insensitive */
	begin
		insert spt_mda values ('IDENTIFIERCASES', 2, 'select 0, 0, 0, 1, 1, 0, 0, 1', 1, 3, 0, -1)
		insert spt_mda values ('IDENTIFIERCASES', 4, '0, 0, 0, 1, 1, 0, 0, 1', 4, 9, 0, -1)
	end
else 		/* case sensitive */
	begin
		insert spt_mda values ('IDENTIFIERCASES', 2, 'select 1, 0, 0, 0, 1, 0, 0, 0', 1, 3, 0, -1)
		insert spt_mda values ('IDENTIFIERCASES', 4, '1, 0, 0, 0, 1, 0, 0, 0', 4, 9, 0, -1)
	end
insert spt_mda values ('SQLKEYWORDS', 2, 'select value from master.dbo.spt_jtext where mdinfo = ''SQLKEYWORDS''', 1, 9, 0, -1)
insert spt_mda values ('NUMERICFUNCTIONLIST', 2, 'select ''abs,acos,asin,atan,atan2,ceiling,cos,cot,degrees,exp,floor,log,log10,pi,power,radians,rand,round,sign,sin,sqrt,tan''', 1, 4, 0, -1)
insert spt_mda values ('NUMERICFUNCTIONLIST', 7, 'abs,acos,asin,atan,atan2,ceiling,cos,cot,degrees,exp,floor,log,log10,pi,power,radians,rand,round,sign,sin,sqrt,tan', 5, 9, 0, -1)
insert spt_mda values ('STRINGFUNCTIONLIST', 2, 'select ''ascii,char,char_length,character_length,concat,difference,insert,length,lcase,ltrim,octet_length,position,repeat,right,rtrim,soundex,space,substring,ucase''', 1, 4, 0, -1)
insert spt_mda values ('STRINGFUNCTIONLIST', 7, 'ascii,char,char_length,character_length,concat,difference,insert,length,lcase,ltrim,octet_length,position,repeat,right,rtrim,soundex,space,substring,ucase', 5, 9, 0, -1)
insert spt_mda values ('SYSTEMFUNCTIONLIST', 2, 'select ''database,ifnull,user,convert''', 1, 4, 0, -1)
insert spt_mda values ('SYSTEMFUNCTIONLIST', 7, 'database,ifnull,user,convert', 5, 9, 0, -1)
insert spt_mda values ('TIMEDATEFUNCTIONLIST', 2, 'select ''curdate,curtime,current_date,current_time,current_timestamp,dayname,dayofmonth,dayofweek,dayofyear,extract,hour,minute,month,monthname,now,quarter,second,timestampadd,timestampdiff,week,year''', 1,4, 0, -1)
insert spt_mda values ('TIMEDATEFUNCTIONLIST', 7, 'curdate,curtime,current_date,current_time,current_timestamp,dayname,dayofmonth,dayofweek,dayofyear,extract,hour,minute,month,monthname,now,quarter,second,timestampadd,timestampdiff,week,year', 5, 9, 0, -1)  
insert spt_mda values ('NULLPLUSNONNULL', 2, 'select 1', 1, 3, 0, -1)
insert spt_mda values ('NULLPLUSNONNULL', 4, '1', 4, 9, 0, -1)
insert spt_mda values ('EXTRANAMECHARS', 2, 'select ''@#$''', 1, 3, 0, -1)
insert spt_mda values ('EXTRANAMECHARS', 6, '@#$', 4, 9, 0, -1)
insert spt_mda values ('MAXBINARYLITERALLENGTH', 2, 'select 255', 1, 3, 0, -1)
insert spt_mda values ('MAXBINARYLITERALLENGTH', 5, '255', 4, 9, 0, -1)
insert spt_mda values ('MAXCHARLITERALLENGTH', 2, 'select 255', 1, 3, 0, -1)
insert spt_mda values ('MAXCHARLITERALLENGTH', 5, '255', 4, 9, 0, -1)
go
insert spt_mda values ('MAXLONGVARBINARYLENGTH', 2, 'select 2147483647', 1, 3, 0, 12000)
insert spt_mda values ('MAXLONGVARBINARYLENGTH', 5, '2147483647', 4, 9, 0, 12000)
insert spt_mda values ('MAXLONGVARBINARYLENGTH', 2, 'select 16384', 1, 3, 12500, -1)
insert spt_mda values ('MAXLONGVARBINARYLENGTH', 5, '16384', 4, 9, 12500, -1)
insert spt_mda values ('MAXLONGVARCHARLENGTH', 2, 'select 2147483647', 1, 3, 0, 12000)
insert spt_mda values ('MAXLONGVARCHARLENGTH', 5, '2147483647', 4, 9, 0, 12000)
insert spt_mda values ('MAXLONGVARCHARLENGTH', 2, 'select 16384', 1, 3, 12500, -1)
insert spt_mda values ('MAXLONGVARCHARLENGTH', 5, '16384', 4, 9, 12500, -1)
insert spt_mda values ('SCHEMAS', 1, 'sp_jdbc_getschemas', 1, 7, 0, -1)
insert spt_mda values ('SCHEMAS', 1, 'sp_jdbc_getschemas(?,?)', 8, 9, 0, -1)
insert spt_mda values ('SCHEMAS_CTS', 1, 'sp_jdbc_getschemas_cts', 1, 9, 0, -1)
insert spt_mda values ('COLUMNPRIVILEGES', 1, 'sp_jdbc_getcolumnprivileges(?,?,?,?)', 1, 9, 0, -1)
insert spt_mda values ('TABLEPRIVILEGES', 1, 'sp_jdbc_gettableprivileges(?,?,?)', 1, 9, 0, -1)
insert spt_mda values ('ROWIDENTIFIERS', 1, 'sp_jdbc_getbestrowidentifier(?,?,?,?,?)', 1, 9, 0, -1)
insert spt_mda values ('VERSIONCOLUMNS', 1, 'sp_jdbc_getversioncolumns(?,?,?)', 1, 9, 0, -1)
insert spt_mda values ('KEYCROSSREFERENCE', 1, 'sp_jdbc_getcrossreferences(?,?,?,?,?,?)', 1, 9, 0, -1)
insert spt_mda values ('INDEXINFO', 1, 'sp_jdbc_getindexinfo(?,?,?,?,?)', 1, 9, 0, -1)
insert spt_mda values ('PROCEDURECOLUMNS', 1, 'sp_jdbc_getprocedurecolumns(?,?,?,?)', 1, 7, 0, -1)
insert spt_mda values ('PROCEDURECOLUMNS', 1, 'sp_jdbc_getprocedurecolumns(?,?,?,?,0,4)', 8, 8, 0, -1)
insert spt_mda values ('PROCEDURECOLUMNS', 1, 'sp_jdbc_getprocedurecolumns(?,?,?,?,?,?)', 9, 9, 0, -1)
insert spt_mda values ('FUNCTIONCOLUMNS', 1, 'sp_jdbc_getfunctioncolumns(?,?,?,?)', 8, 9, 0, -1)
insert spt_mda values ('CATALOGS', 1, 'sp_jdbc_getcatalogs', 1, 9, 0, -1)
insert spt_mda values ('CATALOGS_CTS', 1, 'sp_jdbc_getcatalogs_cts', 1, 9, 0, -1)
insert spt_mda values ('TABLETYPES', 2, 'select TABLE_TYPE from master.dbo.spt_jdbc_table_types', 1, 9, 0, -1)
insert spt_mda values ('SEARCHSTRING', 2, 'select ''\''', 1, 3, 0, -1)
insert spt_mda values ('SEARCHSTRING', 6, '\', 4, 9, 0, -1)
/*
supportsIntegrityEnhancementFacility: true
*/
insert spt_mda values ('INTEGRITYENHANCEMENT', 2, 'select 1', 1, 3, 0, -1)
insert spt_mda values ('INTEGRITYENHANCEMENT', 4, '1', 4, 9, 0, -1)

/* 
supportsOuterJoins: true
supportsFullOuterJoins: false
supportsLimitedOuterJoins: true
supports the syntax of the body of an oj escape without further
processing: false for Version < 12
*/
insert spt_mda values ('OUTERJOINS', 2, 'select 1, 0, 1, 0', 1, 3, 0, 11950)
insert spt_mda values ('OUTERJOINS', 4, '1, 0, 1, 0', 4, 9, 0, 11950)
insert spt_mda values ('OUTERJOINS', 2, 'select 1, 0, 1, 1', 1, 3, 11950, -1)
insert spt_mda values ('OUTERJOINS', 4, '1, 0, 1, 1', 4, 9, 11950, -1)
go

/* 
isCatalogAtStart: true
*/
insert spt_mda values ('CATALOGATSTART', 2, 'select 1', 1, 3, 0, -1)
insert spt_mda values ('CATALOGATSTART', 4, '1', 4, 9, 0, -1)

/* 
same with catalog
*/
insert spt_mda values ('CATALOGSUPPORT', 2, 'select 1, 1, 1, 1, 0', 1, 3, 0, -1)
insert spt_mda values ('CATALOGSUPPORT', 4, '1, 1, 1, 1, 0', 4, 9, 0, -1)

/* 
supportsSubqueriesInComparisons: true
supportsSubqueriesInExists: true
supportsSubqueriesInIns: true
supportsSubqueriesInQuantifieds: true
supportsCorrelatedSubqueries: true
*/
insert spt_mda values ('SUBQUERIES', 2, 'select 1, 1, 1, 1, 1', 1, 3, 0, -1)
insert spt_mda values ('SUBQUERIES', 4, '1, 1, 1, 1, 1', 4, 9, 0, -1)

/*
supportsTableCorrelationNames: true
supportsDifferentTableCorrelationNames: false
*/
insert spt_mda values ('CORRELATIONNAMES', 2, 'select 1, 0', 1, 3, 0, -1)
insert spt_mda values ('CORRELATIONNAMES', 4, '1, 0', 4, 9, 0, -1)

/*
supportsExpressionsInOrderBy: true
supportsOrderByUnrelated: true
*/
insert spt_mda values ('ORDERBYSUPPORT', 2, 'select 1, 1', 1, 3, 0, -1)
insert spt_mda values ('ORDERBYSUPPORT', 4, '1, 1', 4, 9, 0, -1)

/* 
supportsGroupBy: true
supportsGroupByUnrelated: true
supportsGroupByBeyondSelect: true
*/
insert spt_mda values ('GROUPBYSUPPORT', 2, 'select 1, 1, 1', 1, 3, 0, -1)
insert spt_mda values ('GROUPBYSUPPORT', 4, '1, 1, 1', 4, 9, 0, -1)

/* 
supportsMinimumSQLGrammar: true
supportsCoreSQLGrammar: false
supportsExtendedSQLGrammar: false
*/
insert spt_mda values ('SQLGRAMMAR', 2, 'select 1, 0, 0', 1, 3, 0, -1)
insert spt_mda values ('SQLGRAMMAR', 4, '1, 0, 0', 4, 9, 0, -1)

/* 
supportsANSI92EntryLevelSQL: true
supportsANSI92IntermediateSQL: false
supportsANSI92FullSQL: false
*/
insert spt_mda values ('ANSI92LEVEL', 2, 'select 1, 0, 0', 1, 3, 0, -1)
insert spt_mda values ('ANSI92LEVEL', 4, '1, 0, 0', 4, 9, 0, -1)
go

/* 
SQL Server's terms for 'schema', 'procedure' and 'catalog' 
and how to separate them
*/
insert spt_mda values ('SCHEMATERM', 2, 'select ''owner''', 1, 3, 0, -1)
insert spt_mda values ('SCHEMATERM', 6, 'owner', 4, 9, 0, -1)
insert spt_mda values ('PROCEDURETERM', 2, 'select ''stored procedure''', 1, 3, 0, -1)
insert spt_mda values ('PROCEDURETERM', 6, 'stored procedure', 4, 9, 0, -1)
insert spt_mda values ('CATALOGTERM', 2, 'select ''database''', 1, 3, 0, -1)
insert spt_mda values ('CATALOGTERM', 6, 'database', 4, 9, 0, -1)
insert spt_mda values ('CATALOGSEPARATOR', 2, 'select ''.''', 1, 3, 0, -1)
insert spt_mda values ('CATALOGSEPARATOR', 6, '.', 4, 9, 0, -1)

/* 
supportsSchemasInDataManipulation: true
supportsSchemasInProcedureCalls: true
supportsSchemasInTableDefinitions: true
supportsSchemasInIndexDefinitions: true
supportsSchemasInPrivilegeDefinitions: false
*/
insert spt_mda values ('SCHEMASUPPORT', 2, 'select 1, 1, 1, 1, 0', 1, 3, 0, -1)
insert spt_mda values ('SCHEMASUPPORT', 4, '1, 1, 1, 1, 0', 4, 9, 0, -1)

/* 
supportsUnion: true
supportsUnionAll: true
*/
insert spt_mda values ('UNIONSUPPORT', 2, 'select 1, 1', 1, 3, 0, -1)
insert spt_mda values ('UNIONSUPPORT', 4, '1, 1', 4, 9, 0, -1)

/* 
supportsDataDefinitionAndDataManipulationTransactions: true
supportsDataManipulationTransactionsOnly: false
dataDefinitionCausesTransactionCommit: false
dataDefinitionIgnoredInTransactions: false
*/
insert spt_mda values ('TRANSACTIONDATADEFINFO', 2, 'select 1, 0, 0, 0', 1, 3, 0, -1)
insert spt_mda values ('TRANSACTIONDATADEFINFO', 4, '1, 0, 0, 0', 4, 9, 0, -1)

/*
max column name length, max columns in group by,
max columns in index, max columns in order by,
max columns in select, max columns in table
XXX max columns in index is only 15 for B1
server.  Will that be a separate script?  Can we
detect whether this is B1 server?
XXX max columns in select is unlimited, so I'm
returning 0 -- the spec I'm looking at
doesn't actually say what to do in such a case
*/
insert spt_mda values ('COLUMNINFO', 2, 'select 30, 16, 16, 16, 0, 250', 1, 3, 0, -1)
insert spt_mda values ('COLUMNINFO', 5, '30, 16, 16, 16, 0, 250', 4, 9, 0, -1)
insert spt_mda values ('MAXCONNECTIONS', 2, 'select @@max_connections', 1, 9, 0, -1)
insert spt_mda values ('MAXINDEXLENGTH', 2, 'select 255', 1, 3, 0, -1)
insert spt_mda values ('MAXINDEXLENGTH', 5, '255', 4, 9, 0, -1)
/*
max cursor name length, max user name length,
max schema name length, max procedure name length,
max catalog name length
*/
insert spt_mda values ('MAXNAMELENGTHS', 2, 'select 30, 30, 30, 30, 30', 1, 3, 0, -1)
insert spt_mda values ('MAXNAMELENGTHS', 5, '30, 30, 30, 30, 30', 4, 9, 0, -1)
/*
** max bytes in a row is 1962, 0 is for 'no, that doesn't include blobs'
** ROWINFO cannot be converted into a LITERAL type since it contains 
** different types for each column:  int, boolean
*/
insert spt_mda values ('ROWINFO', 2, 'select 1962, 0', 1, 9, 0, -1)
/*
max length of a statement, max number of open statements
both are unlimited
*/
insert spt_mda values ('STATEMENTINFO', 2, 'select 0, 0', 1, 3, 0, -1)
insert spt_mda values ('STATEMENTINFO', 5, '0, 0', 4, 9, 0, -1)
/*
max table name length, max tables in a select
*/
insert spt_mda values ('TABLEINFO', 2, 'select 30, 256', 1, 3, 0, -1)
insert spt_mda values ('TABLEINFO', 5, '30, 256', 4, 9, 0, -1)
go
/*
RSMDA.getColumnTypeName
*/
insert spt_mda values ('COLUMNTYPENAME', 1, 'sp_sql_type_name(?,?)', 1, 9, 0, -1)
go
/*
Get the Data source specific DEFAULT CHARACTER SET
*/
insert spt_mda values ('DEFAULT_CHARSET', 1, 'sp_default_charset', 1, 9, 0, -1)
go
/*
ownUpdatesAreVisible (JDBC 2.0)
TYPE_FORWARD_ONLY: true
TYPE_SCROLL_INSENSITIVE: false
TYPE_SCROLL_SENSITIVE: false
*/
insert spt_mda values ('OWNUPDATESAREVISIBLE', 4, '1, 0, 0', 6, 9, 0, -1)
go
/*
ownDeletesAreVisible (JDBC 2.0)
TYPE_FORWARD_ONLY: true
TYPE_SCROLL_INSENSITIVE: false
TYPE_SCROLL_SENSITIVE: false
*/
insert spt_mda values ('OWNDELETESAREVISIBLE', 4, '1, 0, 0', 6, 9, 0, -1)
go
/*
ownInsertsAreVisible (JDBC 2.0)
TYPE_FORWARD_ONLY: false
TYPE_SCROLL_INSENSITIVE: false
TYPE_SCROLL_SENSITIVE: false
*/
insert spt_mda values ('OWNINSERTSAREVISIBLE', 4, '0, 0, 0', 6, 9, 0, -1)
go
/*
othersUpdatesAreVisible (JDBC 2.0)
TYPE_FORWARD_ONLY: true
TYPE_SCROLL_INSENSITIVE: false
TYPE_SCROLL_SENSITIVE: false
*/
insert spt_mda values ('OTHERSUPDATESAREVISIBLE', 4, '1, 0, 0', 6, 9, 0, -1)
go
/*
othersDeletesAreVisible (JDBC 2.0)
TYPE_FORWARD_ONLY: true
TYPE_SCROLL_INSENSITIVE: false
TYPE_SCROLL_SENSITIVE: false
*/
insert spt_mda values ('OTHERSDELETESAREVISIBLE', 4, '1, 0, 0', 6, 9, 0, -1)
go
/*
othersInsertsAreVisible (JDBC 2.0)
TYPE_FORWARD_ONLY: true
TYPE_SCROLL_INSENSITIVE: false
TYPE_SCROLL_SENSITIVE: false
*/
insert spt_mda values ('OTHERSINSERTSAREVISIBLE', 4, '1, 0, 0', 6, 9, 0, -1)
go
/*
updatesAreDetected (JDBC 2.0)
TYPE_FORWARD_ONLY: false
TYPE_SCROLL_INSENSITIVE: false
TYPE_SCROLL_SENSITIVE: false
*/
insert spt_mda values ('UPDATESAREDETECTED', 4, '0, 0, 0', 6, 9, 0, -1)
go
/*
deletesAreDetected (JDBC 2.0)
TYPE_FORWARD_ONLY: false
TYPE_SCROLL_INSENSITIVE: false
TYPE_SCROLL_SENSITIVE: false
*/
insert spt_mda values ('DELETESAREDETECTED', 4, '0, 0, 0', 6, 9, 0, -1)
go
/*
insertsAreDetected (JDBC 2.0)
TYPE_FORWARD_ONLY: false
TYPE_SCROLL_INSENSITIVE: false
TYPE_SCROLL_SENSITIVE: false
*/
insert spt_mda values ('INSERTSAREDETECTED', 4, '0, 0, 0', 6, 9, 0, -1)
go
/*
supportsBatchUpdates: true (JDBC 2.0)
*/
insert spt_mda values ('SUPPORTSBATCHUPDATES', 4, '1', 6, 9, 0, -1)
go
/*
execBatchUpdatesInLoop: false 
*/
insert spt_mda values ('EXECBATCHUPDATESINLOOP', 4, '0', 6, 9, 0, -1)
go

/*
 * ASE can always execute parameterized batches. Therefore, we do NOT have
 * to execute parameterized batches in a loop.
 */
insert spt_mda values ('EXECPARAMETERIZEDBATCHINLOOP', 4, '0', 6, 9, 0, -1)
go

/*
 * Batches with large numbers of parameters in parameterized batches must
 * be broken up into sub-batches or the server will complain (and the batch
 * won't run)
 */
insert spt_mda values ('MAXBATCHPARAMS', 5, '255', 6, 9, 0, 12000)
insert spt_mda values ('MAXBATCHPARAMS', 5, '2048', 6, 9, 12500, 15700)
insert spt_mda values ('MAXBATCHPARAMS', 5, '32767', 6, 9, 15702, -1)
go


/*
supportsResultSetType (JDBC 2.0)
TYPE_FORWARD_ONLY: true
TYPE_SCROLL_INSENSITIVE: true
TYPE_SCROLL_SENSITIVE: false
*/
insert spt_mda values ('SUPPORTSRESULTSETTYPE', 4, '1, 1, 0', 6, 9, 0, -1)
go
/*
supportsResultSetConcurrency(CONCUR_READ_ONLY) (JDBC 2.0)
TYPE_FORWARD_ONLY: true
TYPE_SCROLL_INSENSITIVE: true
TYPE_SCROLL_SENSITIVE: false
*/
insert spt_mda values ('READONLYCONCURRENCY', 4, '1, 1, 0', 6, 9, 0, -1)
go
/*
supportsResultSetConcurrency(CONCUR_UPDATABLE) (JDBC 2.0)
TYPE_FORWARD_ONLY: true
TYPE_SCROLL_INSENSITIVE: false
TYPE_SCROLL_SENSITIVE: false
*/
insert spt_mda values ('UPDATABLECONCURRENCY', 4, '1, 0, 0', 6, 9, 0, -1)
go
/*
getUDTs (JDBC 2.0)
*/  
insert spt_mda values ('UDTS', 1, 'sp_jdbc_getudts(?,?,?,?)', 6, 9, 0, -1)
go
/*
getSuperTypes (JDBC 3.0)
*/
insert spt_mda values ('SUPERTYPES', 1, 'sp_jdbc_getsupertypes(?,?,?)', 6, 9, 0, -1)
go
/*
getSuperTables (JDBC 3.0)
*/
insert spt_mda values ('SUPERTABLES', 1, 'sp_jdbc_getsupertables(?,?,?)', 6, 9, 0, -1)
go
/*
getAttributes (JDBC 3.0)
*/
insert spt_mda values ('ATTRIBUTES', 1, 'sp_jdbc_getattributes(?,?,?,?)', 6, 9, 0, -1)
go
/*
isCaseSensitive
*/
insert spt_mda values ('ISCASESENSITIVE', 2, 'if exists (select 1 where ''A'' = ''a'') select 0 else select 1', 6, 9, 0, -1)
go

/*
 JTA support for JDBC 2.0 extensions

 XACOORDINATORTYPE returns the type of the distributed transaction
 coordinator.  It is up to jConnect to manufacture an XAResource object
 that can interact with that type of coordinator.

 When ASE 12.0 implements CR 203610 we will use
 that accessor. ASE pre 12.0 returns a dummy resultset.
*/
insert spt_mda values ('XACOORDINATORTYPE', 2, 'select TxnStyle=0, RequiredRole=NULL, Status=0, UniqueID=NULL', 6, 9, 0, 11950)
insert spt_mda values ('XACOORDINATORTYPE', 1, 'sp_jdbc_getxacoordinator', 6, 9, 12000, -1)

/*
Used to detect if surrogate pocessing is supported on server
*/
insert spt_mda values ('SURROGATEPROCESS', 2, 'select value2 from master.dbo.syscurconfigs where config = (select config from master.dbo.sysconfigures where name=''enable surrogate processing'' and parent != 19 and config != 19)', 1, 9, 0, -1)

dump tran sybsystemprocs with truncate_only 
go

/* Sql snippets to create a new lob locator of corresponding type SED_SEARCH_KEYWORD_LOBS157 */
/* Script that creates MDS sprocs for LOB support in ASE 157 and above */

insert spt_mda values ('INIT_TEXTLOCATOR', 2, 'select create_locator(text_locator,null)', 7, 9, 15000, -1)
insert spt_mda values ('INIT_IMAGELOCATOR', 2, 'select create_locator(image_locator,null)', 7, 9, 15000, -1)
insert spt_mda values ('INIT_UNITEXTLOCATOR', 2, 'select create_locator(unitext_locator,null)', 7, 9, 15000, -1)
insert spt_mda values ('INIT_NULL_LOBS', 2, 'select create_locator(image_locator,null), create_locator(text_locator,null), create_locator(unitext_locator,null)', 7, 9, 15000, -1)

insert spt_mda values ('LOB_GETLOB', 1, 'sp_jdbc_getlob(?,?,?,?)', 7, 9, 15000, -1)
insert spt_mda values ('LOB_GETBYTES', 1, 'sp_jdbc_lob_getbytes(?,?,?,?)', 7, 9, 15000, -1)
insert spt_mda values ('TRUNCATE_LOB', 1, 'sp_jdbc_truncate_lob(?,?)', 7, 9, 15000, -1)
insert spt_mda values ('DEALLOCATE_LOCATOR', 1, 'sp_jdbc_deallocate_locator(?)', 7, 9, 15000, -1)
insert spt_mda values ('LOCATOR_VALID', 1, 'sp_jdbc_locator_valid(?,?)', 7, 9, 15000, -1)
insert spt_mda values ('SEARCH_LOB', 1, 'sp_jdbc_search_lob(?,?,?,?,?,?,?)', 7, 9, 15000, -1)
insert spt_mda values ('LOB_LENGTH', 1, 'sp_jdbc_lob_length(?,?,?)', 7, 9, 15000, -1)
insert spt_mda values ('TEXT_SETDATA', 1, 'sp_jdbc_text_setdata(?,?,?,?)', 7, 9, 15000, -1)
insert spt_mda values ('UNITEXT_SETDATA', 1, 'sp_jdbc_unitext_setdata(?,?,?,?)', 7, 9, 15000, -1)
insert spt_mda values ('IMAGE_SETDATA', 1, 'sp_jdbc_image_setdata(?,?,?,?)', 7, 9, 15000, -1)


/*
**  sp_jdbc_getlob
*/

use sybsystemprocs
go

if exists (select * from sysobjects where name = 'sp_jdbc_getlob')
    begin
        drop procedure sp_jdbc_getlob
    end
go

create procedure sp_jdbc_getlob (
    @lob_type int,
    @locator binary(24),
    @read_pos bigint = 0,
    @read_len int = -1)

as
    if @lob_type = 0
        begin
	        if @read_len < 0
	            select return_lob(IMAGE, @locator)
	        else
	            select return_lob(IMAGE, substring(locator_literal(IMAGE_locator,@locator), @read_pos, @read_len))
        end
    else if @lob_type = 1
        begin
	        if @read_len < 0
	            select return_lob(TEXT, @locator)
	        else
	            select return_lob(TEXT, substring(locator_literal(TEXT_locator,@locator), @read_pos, @read_len))
        end
    else
        begin
	       if @read_len < 0
	           select return_lob(UNITEXT, @locator)
	       else
	           select return_lob(UNITEXT, substring(locator_literal(UNITEXT_locator,@locator), @read_pos, @read_len))
        end
go


exec sp_procxmode 'sp_jdbc_getlob', 'anymode'
go

grant execute on sp_jdbc_getlob to public
go

dump transaction sybsystemprocs with truncate_only
go

/*
**  End of sp_jdbc_getlob
*/

/*
**  sp_jdbc_lob_getbytes
*/

use sybsystemprocs
go

if exists (select * from sysobjects where name = 'sp_jdbc_lob_getbytes')
    begin
        drop procedure sp_jdbc_lob_getbytes
    end
go

create procedure sp_jdbc_lob_getbytes (
    @lob_type int,
    @locator binary(24),
    @read_pos bigint,
    @read_len int)
as
    if @lob_type = 0
        select substring(locator_literal(IMAGE_locator,@locator),@read_pos,@read_len), datalength(locator_literal(IMAGE_locator,@locator))
    else if @lob_type = 1
        select substring(locator_literal(TEXT_locator,@locator),@read_pos,@read_len), char_length(locator_literal(TEXT_locator,@locator))
    else
        select substring(locator_literal(UNITEXT_locator,@locator),@read_pos,@read_len), char_length(locator_literal(UNITEXT_locator,@locator))
go

exec sp_procxmode 'sp_jdbc_lob_getbytes', 'anymode'
go

grant execute on sp_jdbc_lob_getbytes to public
go

dump transaction sybsystemprocs with truncate_only
go

/*
**  sp_jdbc_truncate_lob
*/

use sybsystemprocs
go

if exists (select * from sysobjects where name = 'sp_jdbc_truncate_lob')
    begin
        drop procedure sp_jdbc_truncate_lob
    end
go

create procedure sp_jdbc_truncate_lob (
    @locator binary(24),
    @len int)
as
    truncate lob @locator ( @len )
go

exec sp_procxmode 'sp_jdbc_truncate_lob', 'anymode'
go

grant execute on sp_jdbc_truncate_lob to public
go

dump transaction sybsystemprocs with truncate_only
go

/*
**  End of sp_jdbc_truncate_lob
*/

/*
**  sp_jdbc_deallocate_locator
*/

use sybsystemprocs
go

if exists (select * from sysobjects where name = 'sp_jdbc_deallocate_locator')
    begin
        drop procedure sp_jdbc_deallocate_locator
    end
go

create procedure sp_jdbc_deallocate_locator (
        @locator binary(24))
as
    deallocate locator @locator
go

exec sp_procxmode 'sp_jdbc_deallocate_locator', 'anymode'
go

grant execute on sp_jdbc_deallocate_locator to public
go

dump transaction sybsystemprocs with truncate_only
go

/*
**  End of sp_jdbc_deallocate_locator
*/

/*
**  sp_jdbc_locator_valid
*/

use sybsystemprocs
go

if exists (select * from sysobjects where name = 'sp_jdbc_locator_valid')
    begin
        drop procedure sp_jdbc_locator_valid
    end
go

create procedure sp_jdbc_locator_valid (
        @locator binary(24),
        @valid int output)
as
    select @valid = locator_valid( @locator )
go

exec sp_procxmode 'sp_jdbc_locator_valid', 'anymode'
go

grant execute on sp_jdbc_locator_valid to public
go

dump transaction sybsystemprocs with truncate_only
go

/*
**  End of sp_jdbc_locator_valid
*/

/*
**  sp_jdbc_search_lob
*/

use sybsystemprocs
go

if exists (select * from sysobjects where name = 'sp_jdbc_search_lob')
    begin
        drop procedure sp_jdbc_search_lob
    end
go


create procedure sp_jdbc_search_lob (
    @lob_type int,
    @search varbinary(16384),
    @locator binary(24),
    @pos bigint,
    @result bigint output,
    @isPattern int = 0,
    @searchString varchar(16384) = null)
as
    if @lob_type = 0
        if @isPattern = 0
            select @result = charindex (locator_literal(IMAGE_locator,@search), substring(locator_literal(IMAGE_locator, @locator),@pos,datalength(locator_literal(IMAGE_locator,@locator))-@pos+1))+@pos-1
        else
            select @result = charindex (@search, substring(locator_literal(IMAGE_locator,@locator),@pos,datalength(locator_literal(IMAGE_locator,@locator))-@pos+1))+@pos-1
    else if @lob_type = 1
        if @isPattern = 0
            select @result = charindex (locator_literal(TEXT_locator,@search), substring(locator_literal(TEXT_locator,@locator),@pos,char_length(locator_literal(TEXT_locator,@locator))-@pos+1))+@pos-1
        else
            select @result = charindex (@searchString, substring(locator_literal(TEXT_locator,@locator),@pos,char_length(locator_literal(TEXT_locator,@locator))-@pos+1))+@pos-1
    else
        if @isPattern = 0
            select @result = charindex (locator_literal(UNITEXT_locator,@search), substring(locator_literal(UNITEXT_locator,@locator),@pos,char_length(locator_literal(UNITEXT_locator,@locator))-@pos+1))+@pos-1
        else
            select @result = charindex (@searchString, substring(locator_literal(UNITEXT_locator,@locator),@pos,char_length(locator_literal(UNITEXT_locator,@locator))-@pos+1))+@pos-1
go

exec sp_procxmode 'sp_jdbc_search_lob', 'anymode'
go

grant execute on sp_jdbc_search_lob to public
go

dump transaction sybsystemprocs with truncate_only
go

/*
**  End of sp_jdbc_search_lob
*/

/*
**  sp_jdbc_image_setdata
*/

use sybsystemprocs
go

if exists (select * from sysobjects where name = 'sp_jdbc_image_setdata')
    begin
        drop procedure sp_jdbc_image_setdata
    end
go


CREATE PROCEDURE sp_jdbc_image_setdata(@locator binary(24), @offset bigint, @new_data image, @data_length INT OUTPUT) AS BEGIN
    SELECT @data_length = setdata(locator_literal(IMAGE_locator,@locator), @offset, @new_data)
END

go

exec sp_procxmode 'sp_jdbc_image_setdata', 'anymode'
go

grant execute on sp_jdbc_image_setdata to public
go

dump transaction sybsystemprocs with truncate_only
go

use sybsystemprocs
go

if exists (select * from sysobjects where name = 'sp_jdbc_unitext_setdata')
    begin
        drop procedure sp_jdbc_unitext_setdata
    end
go

CREATE PROCEDURE sp_jdbc_unitext_setdata(@locator BINARY(24), @offset bigint, @new_data UNITEXT, @data_length INT OUTPUT) AS BEGIN
    SELECT @data_length = setdata(locator_literal(UNITEXT_LOCATOR,@locator), @offset, @new_data)
END

go

exec sp_procxmode 'sp_jdbc_unitext_setdata', 'anymode'
go

grant execute on sp_jdbc_unitext_setdata to public
go

dump transaction sybsystemprocs with truncate_only
go



use sybsystemprocs
go

if exists (select * from sysobjects where name = 'sp_jdbc_text_setdata')
    begin
        drop procedure sp_jdbc_text_setdata
    end
go

CREATE PROCEDURE sp_jdbc_text_setdata(@locator binary(24), @offset bigint, @new_data text, @data_length INT OUTPUT) AS BEGIN
    SELECT @data_length = setdata(locator_literal(TEXT_locator,@locator), @offset, @new_data)
END

go

exec sp_procxmode 'sp_jdbc_text_setdata', 'anymode'

grant execute on sp_jdbc_text_setdata to public
go

dump transaction sybsystemprocs with truncate_only
go


/*
**  sp_jdbc_lob_length
*/

use sybsystemprocs
go

if exists (select * from sysobjects where name = 'sp_jdbc_lob_length')
    begin
        drop procedure sp_jdbc_lob_length
    end
go

create procedure sp_jdbc_lob_length (
        @lob_type int,
        @locator binary(24),
        @result bigint output)
as
    if @lob_type = 0
        select @result = datalength(locator_literal(IMAGE_locator,@locator))
    else if @lob_type = 1
        select @result = char_length(locator_literal(TEXT_locator,@locator))
    else
        select @result = char_length(locator_literal(UNITEXT_locator,@locator))
go

exec sp_procxmode 'sp_jdbc_lob_length', 'anymode'
go

grant execute on sp_jdbc_lob_length to public
go

dump transaction sybsystemprocs with truncate_only
go


/*
**  End of sp_jdbc_lob_length
*/


/*
**  End of spt_mda
*/


/*
**  spt_jtext
*/

/** SECTION BEGIN: CLEANUP **/
use master
go

if exists (select * from sysobjects where name = 'spt_jtext')
begin
	drop table spt_jtext
end
go
/** SECTION END: CLEANUP **/

create table spt_jtext (mdinfo varchar(30) unique, value text)
go

grant select on spt_jtext to public
go

/* Don't delete the following line. It is the checkpoint for sed */
/* Server dependent stored procedure add here ad ADDPOINT_SQL_KEYWORDS */


/*
 * To populate the spt_jtext table in 12.5, we need to use a temporary stored
 * procedure. This is because the values we want are stored in a table, and
 * we'd like to parse them into a comma-separated list and then place them
 * in the spt_jtext table so they're all ready and waiting when the jdbc
 * client calls for them. They need to be in the format of a comma-separated
 * list, because that is what is called for in the jdbc spec.
 */

if exists (select * from sysobjects where name = 'sp_jdbc_temp_sqlkeyword_proc')
begin
    drop proc sp_jdbc_temp_sqlkeyword_proc
end

go

create proc sp_jdbc_temp_sqlkeyword_proc
as
    declare @keywords    varchar (1900)  /* we want this as big as possible to
                                            handle future additions to the
                                            keyword table.  */
    declare @word        varchar (255)

    declare @first       bit             /* a flag */

    select @first = 1

    declare keyword_cursor cursor for
        select upper(name) from master.dbo.spt_values
          where type = 'W' and ansi_w = 0 order by name

    open keyword_cursor

    fetch keyword_cursor into @word

    while (@@sqlstatus != 2)
    begin
        if (@first = 1)
        begin
            select @keywords = @word
            select @first = 0
        end
    
        else
        begin
            select @keywords = @keywords + ',' + @word
        end

        fetch keyword_cursor into @word
    end

    close keyword_cursor

    /* Now that we've built up our comma-separated list, stick this into
       the already-created spt_jtext table */

    insert master.dbo.spt_jtext values ('SQLKEYWORDS', @keywords)

go

/* Now, we want to execute the temporary stored proc to populate the spt_jtext
   table */

exec sp_jdbc_temp_sqlkeyword_proc
go

/* Now that it's served its purpose, remove the proc */
if exists (select * from sysobjects where name = 'sp_jdbc_temp_sqlkeyword_proc')
begin
    drop proc sp_jdbc_temp_sqlkeyword_proc
end

go




commit
go
dump tran sybsystemprocs with truncate_only
go


/*
**  End of spt_jtext
*/


/*
**  spt_jdbc_conversion
*/


/** SECTION BEGIN: CLEANUP **/
use master
go

/*
** create table with conversion information
*/
if exists (select * from sysobjects
	where name = 'spt_jdbc_conversion')
begin
	drop table spt_jdbc_conversion
end
go
/** SECTION END: CLEANUP **/

create table spt_jdbc_conversion (datatype int, conversion char(20))
go

grant select on spt_jdbc_conversion to public
go

/*Values based on the table info from the SQL Server Ref Man Chapter 4*/
/*bit*/
insert into spt_jdbc_conversion values(0,'11111110111111110001')
/*integers+numerics*/
insert into spt_jdbc_conversion values(1,'11111100011111110000')
insert into spt_jdbc_conversion values(2,'11111100011111110000')
insert into spt_jdbc_conversion values(9,'11111100011111110000')
insert into spt_jdbc_conversion values(10,'11111100011111110000')
insert into spt_jdbc_conversion values(11,'11111100011111110000')
insert into spt_jdbc_conversion values(12,'11111100011111110000')
insert into spt_jdbc_conversion values(13,'11111100011111110000')
insert into spt_jdbc_conversion values(14,'11111100011111110000')
insert into spt_jdbc_conversion values(15,'11111100011111110000')
/*Binaries*/
insert into spt_jdbc_conversion values(5,'11111110111111111111')
insert into spt_jdbc_conversion values(4,'11111110111111111111')
insert into spt_jdbc_conversion values(3,'11111110111111111111')
/*Characters*/
insert into spt_jdbc_conversion values(6,'00011110100000001111')
insert into spt_jdbc_conversion values(8,'00011110100000001111')
insert into spt_jdbc_conversion values(19,'00011110100000001111')
/*Dates*/
insert into spt_jdbc_conversion values(16,'00000010000000001110')
insert into spt_jdbc_conversion values(17,'00000010000000001110')
insert into spt_jdbc_conversion values(18,'00000010000000001110')
/*NULL*/
insert into spt_jdbc_conversion values(7,'00000000000000000000')
go
commit
go
dump tran sybsystemprocs with truncate_only 
go

/*
**  End of spt_jdbc_conversion
*/


/*
**  sp_mda
*/


/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs
go

/*
** create the well-known sp_mda procedure for accessing the data
*/
if exists (select * from sysobjects where name = 'sp_mda')
begin
	drop procedure sp_mda
end
go
/** SECTION END: CLEANUP **/


/*
** requesttype 0 == Returns the mdinfo:MDAVERSION and mdinfo:MDARELEASEID rows.
** requesttype 1 == JDBC
** requesttype 2 == JDBC - but only send back the minimal frequently used info.
** 
** mdaversion 
*/
create procedure sp_mda(@requesttype int, @requestversion int, @clientversion int = 0) as
begin

	declare @min_mdaversion int, @max_mdaversion int
	declare @mda_version int
	declare @srv_version int
	declare @mdaver_querytype tinyint
	declare @mdaver_query varchar(255)
	declare @orginal_isolation_level int

	select @min_mdaversion = 1
	select @max_mdaversion = 9
	select @mda_version = @requestversion


	if @@trancount = 0
	begin
		set chained off
	end
	select @orginal_isolation_level=@@isolation 
	set transaction isolation level 1

	/* this will make sure that all rows are sent even if
	** the client "set rowcount" is differect
	*/

	set rowcount 0

	/* get the Server version */
	/* Server dependent select ADDPOINT_ASEVERSION*/
        select @srv_version=@@version_as_integer

	/*
	** if the client is requesting a version too old
	** then we return our lowest version supported
	**
	** the client needs to be able to just handle this
	*/
	if (@requestversion < @min_mdaversion)
		begin
			select @mda_version = @min_mdaversion
		end

	/*
	** if the client is requesting a version too new
	** we will return our highest version available
	*/
	if (@mda_version > @max_mdaversion)
		begin
			select @mda_version = @max_mdaversion
		end

	/*
	** if the client's requested version is between 1 and 3, 
	** then the mda version returned needs to be 1.  The reason
	** for this is the jConnect driver would pass in it's own 
	** major version number as the @requestversion.  We need to
	** keep older version's of the driver working ok since 
	** they expect a '1' to be returned.
	*/
	if (@mda_version < 4)
		begin
			select @mda_version = 1
			select @mdaver_querytype = 2
			select @mdaver_query = 'select 1'
		end
	else
		begin
			select @mdaver_querytype = 5
			select @mdaver_query = convert(varchar(255), @mda_version)
		end

	/*
	** process the @requesttype
	*/
	if (@requesttype = 0)
		begin
			select "mdinfo" = convert(varchar(30),'MDAVERSION'), 
				   "querytype" = @mdaver_querytype,
				   "query" = @mdaver_query
			union
			select mdinfo, querytype, query 
			from master..spt_mda
			where mdinfo in (
				'MDARELEASEID'
				)
		end
	else if (@requesttype = 1)
		begin
			select "mdinfo" = convert(varchar(30),'MDAVERSION'), 
				   "querytype" = @mdaver_querytype,
				   "query" = @mdaver_query
			union
			select mdinfo, querytype, query
			from master..spt_mda
			where @mda_version >= mdaver_start
			  and @mda_version <= mdaver_end
			  and ((@srv_version >= srvver_start) 
					  and (@srv_version <= srvver_end 
					  or srvver_end = -1))
		end
	else if (@requesttype = 2)
		begin
			select "mdinfo" = convert(varchar(30),'MDAVERSION'), 
				   "querytype" = @mdaver_querytype,
				   "query" = @mdaver_query
			union
			select mdinfo, querytype, query 
			from master..spt_mda
			where mdinfo in (
				'CONNECTCONFIG', 
			'SET_CATALOG',
			'SET_AUTOCOMMIT_ON',
			'SET_AUTOCOMMIT_OFF',
			'SET_ISOLATION',
			'SET_ROWCOUNT',
				'DEFAULT_CHARSET'
				)
			and @mda_version >= mdaver_start
			and @mda_version <= mdaver_end
			and ((@srv_version >= srvver_start) 
					and (@srv_version <= srvver_end 
					or srvver_end = -1))
		end

	-- default isolation level for ASE is 1    
	if (@orginal_isolation_level = 0)
	begin
		set transaction isolation level 0
	end
	if (@orginal_isolation_level = 2)
	begin
		set transaction isolation level 2
	end
	if (@orginal_isolation_level = 3)
	begin
		set transaction isolation level 3
	end
end
go

exec sp_procxmode 'sp_mda', 'anymode'
go
grant execute on sp_mda to public
go
dump transaction sybsystemprocs with truncate_only
go

/*
**  End of sp_mda
*/


/*
**  jdbc_function_escapes
*/


/* 
** This script creates a table which is used by jdbcCONNECT to
** obtain information on this specific server types implementation
** of the various static functions for which JDBC provides escape
** sequences.
**
** Each row has two columns.  Escape_name is the name of the
** static function escape.  Map_string is a string showing how the
** function call should be sent to the server.  %i is a placeholder
** for the i'th argument to the escape.  This numbering is used
** to support skipping arguments.  Reordering of arguments is NOT
** supported.  Thus, a map string of 'foo(%2)' is ok (skips first
** argument); 'foo(%2, %1)' is not ok, at least until the driver
** changes to support this.
**
** Don't include rows for unsupported functions.
**
** Three escapes, convert, timestampadd, and timestampdiff, have
** one argument which takes special constant values.  These constants
** may also need to be mapped.  Therefore, include one row for each
** possible constant value, using the concatenation of the function name
** and the constant value as the escape_name column.  E.g.: 
** convertsql_binary, convertsql_bit, convertsql_char, etc.
** DO count the constant in figuring argument numbers.  Thus,
** timestampadd(sql_tsi_second, ts, ts) gets the map string
** 'dateadd(ss, %2, %3)')
**
** Use lower case for the escape name.  Use whatever case you
** need to for the map string.
**
*/

/** SECTION BEGIN: CLEANUP **/
use master
go

if exists (select * from sysobjects
	where name = 'jdbc_function_escapes')
	begin
		drop table jdbc_function_escapes
	end
go
/** SECTION END: CLEANUP **/

create table jdbc_function_escapes (escape_name varchar(40),
	map_string varchar(254))
go

grant select on jdbc_function_escapes to public
go

/* don't bother inserting rows for unsupported functions
** insert jdbc_function_escapes values ('mod', null)
** insert jdbc_function_escapes values ('truncate', null)
** insert jdbc_function_escapes values ('left', null)
** insert jdbc_function_escapes values ('replace', null)
** insert jdbc_function_escapes values (timestampdiffsql_tsi_frac_second, null)
** insert jdbc_function_escapes values (timestampaddsql_tsi_frac_second, null)
** insert jdbc_function_escapes values ('convertsql_bigint', null)
*/
insert jdbc_function_escapes values ('abs', 'abs(%1)')
go
insert jdbc_function_escapes values ('acos', 'acos(%1)')
go
insert jdbc_function_escapes values ('asin', 'asin(%1)')
go
insert jdbc_function_escapes values ('atan', 'atan(%1)')
go
insert jdbc_function_escapes values ('atan2', 'atn2(%1, %2)')
go
insert jdbc_function_escapes values ('ceiling', 'ceiling(%1)')
go
insert jdbc_function_escapes values ('cos', 'cos(%1)')
go
insert jdbc_function_escapes values ('cot', 'cot(%1)')
go
insert jdbc_function_escapes values ('degrees', 'degrees(%1)')
go
insert jdbc_function_escapes values ('exp', 'exp(%1)')
go
insert jdbc_function_escapes values ('floor', 'floor(%1)')
go
insert jdbc_function_escapes values ('locate', 'charindex ((convert (varchar, %1)), (convert (varchar, %2)))')
go
insert jdbc_function_escapes values ('log', 'log(%1)')
go
insert jdbc_function_escapes values ('log10', 'log10(%1)')
go
insert jdbc_function_escapes values ('pi', 'pi()')
go
insert jdbc_function_escapes values ('power', 'power(%1, %2)')
go
insert jdbc_function_escapes values ('radians', 'radians(%1)')
go
insert jdbc_function_escapes values ('rand', 'rand(%1)')
go
insert jdbc_function_escapes values ('round', 'round(%1, %2)')
go
insert jdbc_function_escapes values ('sign', 'sign(%1)')
go
insert jdbc_function_escapes values ('sin', 'sin(%1)')
go
insert jdbc_function_escapes values ('sqrt', 'sqrt(%1)')
go
insert jdbc_function_escapes values ('tan', 'tan(%1)')
go
insert jdbc_function_escapes values ('ascii', 'ascii(%1)')
go
insert jdbc_function_escapes values ('char', 'char(%1)')
go
insert jdbc_function_escapes values ('concat', '%1 + %2')
go
insert jdbc_function_escapes values ('difference', 'difference(%1, %2)')
go
insert jdbc_function_escapes values ('insert', 'stuff(%1, %2, %3, %4)')
go
insert jdbc_function_escapes values ('length', 'char_length(%1)')
go
insert into jdbc_function_escapes values ('character_length', 'char_length(%1)')
go
insert into jdbc_function_escapes values ('char_length', 'char_length(%1)')
go
insert into jdbc_function_escapes values ('octet_length', 'octet_length(%1)')
go
insert jdbc_function_escapes values ('lcase', 'lower(%1)')
go
insert jdbc_function_escapes values ('ltrim', 'ltrim(%1)')
go
insert jdbc_function_escapes values ('repeat', 'replicate(%1, %2)')
go
insert jdbc_function_escapes values ('right', 'right(%1, %2)')
go
insert jdbc_function_escapes values ('rtrim', 'rtrim(%1)')
go
insert jdbc_function_escapes values ('soundex', 'soundex(%1)')
go
insert jdbc_function_escapes values ('space', 'space(%1)')
go
insert jdbc_function_escapes values ('substring', 'substring(%1, %2, %3)')
go
insert jdbc_function_escapes values ('ucase', 'upper(%1)')
go

/* Don't delete the following line. This is where current date time fucntions will be inserted  gets inserted. */
/*** ADDPOINT_CURRENTDATETIMEFUNCTIONS ***/

/***  SECTION BEGIN : JDBC current_datetime_escape_functions ***/

insert into jdbc_function_escapes values ('curdate', 'current_date()')
go
insert into jdbc_function_escapes values ('current_date', 'current_date()')
go
insert into jdbc_function_escapes values ('curtime', 'current_time()')
go
insert into jdbc_function_escapes values ('current_time', 'current_time()')
go

/***  SECTION END : JDBC current_datetime_escape_functions ***/

insert into jdbc_function_escapes values ('current_timestamp', 'getdate()')
go
insert jdbc_function_escapes values ('dayname', 'datename(dw, %1)')
go
insert jdbc_function_escapes values ('dayofmonth', 
	'datepart(dd, %1)')
go
insert jdbc_function_escapes values ('dayofweek', 
	'datepart(dw, %1)')
go
insert jdbc_function_escapes values ('dayofyear', 
	'datepart(dy, %1)')
go
insert jdbc_function_escapes values ('hour', 'datepart(hh, %1)')
go
insert jdbc_function_escapes values ('minute', 'datepart(mi, %1)')
go
insert jdbc_function_escapes values ('month', 'datepart(mm, %1)')
go
insert jdbc_function_escapes values ('monthname',    
	'datename(mm, %1)')
go
insert jdbc_function_escapes values ('now', 'getdate()')
go
insert jdbc_function_escapes values ('quarter', 'datepart(qq, %1)')
go
insert jdbc_function_escapes values ('second', 'datepart(ss, %1)')
go
insert jdbc_function_escapes values ('timestampaddsql_tsi_second',
	'dateadd(ss, %2, %3)')
go
insert jdbc_function_escapes values ('timestampaddsql_tsi_minute',
	'dateadd(mi, %2, %3)')
go
insert jdbc_function_escapes values ('timestampaddsql_tsi_hour',
	'dateadd(hh, %2, %3)')
go
insert jdbc_function_escapes values ('timestampaddsql_tsi_day',
	'dateadd(dd, %2, %3)')
go
insert jdbc_function_escapes values ('timestampaddsql_tsi_week',
	'dateadd(wk, %2, %3)')
go
insert jdbc_function_escapes values ('timestampaddsql_tsi_month',
	'dateadd(mm, %2, %3)')
go
insert jdbc_function_escapes values ('timestampaddsql_tsi_quarter',
	'dateadd(qq, %2, %3)')
go
insert jdbc_function_escapes values ('timestampaddsql_tsi_year',
	'dateadd(yy, %2, %3)')
go
insert jdbc_function_escapes values ('timestampdiffsql_tsi_second',
	'datediff(ss, %2, %3)')
go
insert jdbc_function_escapes values ('timestampdiffsql_tsi_minute',
	'datediff(mi, %2, %3)')
go
insert jdbc_function_escapes values ('timestampdiffsql_tsi_hour',
	'datediff(hh, %2, %3)')
go
insert jdbc_function_escapes values ('timestampdiffsql_tsi_day',
	'datediff(dd, %2, %3)')
go
insert jdbc_function_escapes values ('timestampdiffsql_tsi_week',
	'datediff(wk, %2, %3)')
go
insert jdbc_function_escapes values ('timestampdiffsql_tsi_month',
	'datediff(mm, %2, %3)')
go
insert jdbc_function_escapes values ('timestampdiffsql_tsi_quarter',
	'datediff(qq, %2, %3)')
go
insert jdbc_function_escapes values ('timestampdiffsql_tsi_year',
	'datediff(yy, %2, %3)')
go
insert jdbc_function_escapes values ('week', 'datepart(wk, %1)')
go
insert jdbc_function_escapes values ('year', 'datepart(yy, %1)')
go
insert jdbc_function_escapes values ('database', 'db_name()')
go
insert jdbc_function_escapes values ('ifnull', 'isnull(%1, %2)')
go
insert jdbc_function_escapes values ('user', 'user_name()')
go
insert jdbc_function_escapes values ('convertsql_binary',
	'convert(varbinary(255), %1)')
go
insert jdbc_function_escapes values ('convertsql_bit', 
	'convert(bit, %1)')
go
insert jdbc_function_escapes values ('convertsql_char',
	'convert(varchar(255), %1)')
go
insert jdbc_function_escapes values ('convertsql_date',
	'convert(datetime, %1)')
go
insert jdbc_function_escapes values ('convertsql_decimal',
	'convert(decimal(36, 18), %1)')
go
insert jdbc_function_escapes values ('convertsql_double',
	'convert(float, %1)')
go
insert jdbc_function_escapes values ('convertsql_float',
	'convert(float, %1)')
go
insert jdbc_function_escapes values ('convertsql_integer',
	'convert(int, %1)')
go
insert jdbc_function_escapes values ('convertsql_longvarbinary',
	'convert(varbinary(255), %1)')
go
insert jdbc_function_escapes values ('convertsql_longvarchar',
	'convert(varchar(255), %1)')
go
insert jdbc_function_escapes values ('convertsql_real',
	'convert(real, %1)')
go
insert jdbc_function_escapes values ('convertsql_smallint',
	'convert(smallint, %1)')
go
insert jdbc_function_escapes values ('convertsql_time',
	'convert(datetime, %1)')
go
insert jdbc_function_escapes values ('convertsql_timestamp',
	'convert(datetime, %1)')
go
insert jdbc_function_escapes values ('convertsql_tinyint',
	'convert(tinyint, %1)')
go
insert jdbc_function_escapes values ('convertsql_varbinary',
	'convert(varbinary(255), %1)')
go
insert jdbc_function_escapes values ('convertsql_varchar',
	'convert(varchar(255), %1)')
go

/*
**  End of jdbc_function_escapes
*/



/*
**  sp_jdbc_convert_datatype
*/

/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects where name = 'sp_jdbc_convert_datatype')
	begin
		drop procedure sp_jdbc_convert_datatype
	end
go
/** SECTION END: CLEANUP **/

create procedure sp_jdbc_convert_datatype (
					@source int,
					@destination int)
as

	if @@trancount = 0
	begin
		set chained off
	end

	set transaction isolation level 1

	/* Make source non-negative */
	select @source = @source + 7
	/* Put the strange date numbers into this area between 0-19*/
	if (@source > 90)
		select @source = @source - 82

	/*Convert destination the same way*/
	/* Put the strange date numbers into this area between 0-19*/
	if (@destination > 90)
		select @destination = @destination - 82

	/* Need 8 added instead of 7 because substring starts at 1 instead */
	/* of 0 */
	select @destination = @destination + 8

	/* Check the conversion. If the bit string in the table has a 1 
	** on the place's number of the destination's value we have to 
	** return true, else false
	*/
	if ((select substring(conversion,@destination,1)
		from master.dbo.spt_jdbc_conversion
		where datatype = @source) = '1')

		select 1
	else 
		select 0
go

exec sp_procxmode 'sp_jdbc_convert_datatype', 'anymode'
go

grant execute on sp_jdbc_convert_datatype to public
go

commit
go

/*
**  End of sp_jdbc_convert_datatype
*/


/*
**  sp_jdbc_function_escapes
*/


/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects where name =
	'sp_jdbc_function_escapes')
	begin
		drop procedure sp_jdbc_function_escapes
	end
go
/** SECTION END: CLEANUP **/

create procedure sp_jdbc_function_escapes
as

	if @@trancount = 0
	begin
		set chained off
	end

	set transaction isolation level 1

	/* this will make sure that all rows are sent even if
	** the client "set rowcount" is differect
	*/

	set rowcount 0

	select * 
	from master.dbo.jdbc_function_escapes

go

exec sp_procxmode 'sp_jdbc_function_escapes', 'anymode'
go

grant execute on sp_jdbc_function_escapes to public
go

/*
**  End of sp_jdbc_function_escapes
*/



/*
**  sp_jdbc_fkeys
*/

/* The following code is taken from the ODBC handling of 
 * primary keys, and foreign keys, and modified slightly 
 * for JDBC compliance.
 * ODBC sp_fkeys --> sp_jdbc_fkeys
 *      sp_pkeys --> sp_jdbc_pkeys
 *      #pid     --> #jpid
 *      #fid     --> #fid
 */


/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select *
		from sysobjects
				where sysstat & 7 = 4
						and name = 'sp_jdbc_fkeys')
begin
		drop procedure sp_jdbc_fkeys
end
go
/** SECTION END: CLEANUP **/

/*
** parameters: @pktable_name - table name for primary key
**             @pktable_owner - (schema) a schema name pattern; "" retrieves 
**		those without a schema
**             @pktable_qualifier - (catalog name) a catalog name; "" retrieves
**              those without a catalog; null means drop catalog name from the
**                  selection criteria 
**             @fktable_name - table name for foreign key
**             @fktable_owner - (schema) a schema name pattern; "" retrieves 
**		those  without a schema
**             @fktable_qualifier - (catalog name) a catalog name; "" retrieves
**              those without a catalog; null means drop catalog name from the
**              selection criteria 
**
** note: there is one raiserror message: 18040
**
** messages for 'sp_jdbc_fkeys'               18039, 18040
**
** 17461, 'Object does not exist in this database.'
** 18040, 'Catalog procedure %1! can not be run in a transaction.', sp_jdbc_fkeys
** 18043 ' Primary key table name or foreign key table name or both must be
** given'
** 18044, '%1! table qualifier must be name of current database.' [Primary
** key | Foreign key]
**
*/

CREATE PROCEDURE sp_jdbc_fkeys
	@pktable_name       varchar(300) = null,
	@pktable_owner      varchar(32 ) = null,
	@pktable_qualifier  varchar(32 ) = null,
	@fktable_name       varchar(300) = null,
	@fktable_owner      varchar(32 ) = null,
	@fktable_qualifier  varchar(32 ) = null
AS
/* Don't delete the following line. It is the checkpoint for sed */
/* Server dependent stored procedure add here ad ADDPOINT_FKEYS */
    declare @ftabid int, @ptabid int, @constrid int, @keycnt int, @primkey int
    declare @fokey1 int, @fokey2 int,  @fokey3 int,  @fokey4 int, @fokey5  int
    declare @fokey6 int, @fokey7 int,  @fokey8 int,  @fokey9 int, @fokey10 int
    declare @fokey11 int,@fokey12 int, @fokey13 int, @fokey14 int,@fokey15 int
    declare @refkey1 int,@refkey2 int, @refkey3 int, @refkey4 int,@refkey5  int
    declare @refkey6 int,@refkey7 int, @refkey8 int, @refkey9 int,@refkey10 int
    declare @refkey11 int, @refkey12 int, @refkey13 int, @refkey14 int
    declare @refkey15 int, @refkey16 int, @fokey16 int, @status int, @i int
    declare @msg varchar(255)
    declare @msg2 varchar(50)
    declare @export int, @import int
    declare @notDeferrable int 
    declare @startedInTransaction bit 

    if (@@trancount = 0)
    begin
        set chained off
    end

    /* check if we're in a transaction before we execute any selects */
    if (@@trancount > 0)
        select @startedInTransaction = 1
    else
        select @startedInTransaction = 0

    /* this will make sure that all rows are sent even if
    ** the client "set rowcount" is differect
    */

    set rowcount 0


    select @notDeferrable = 7       
    select @import = 0
    select @export = 0
    
    /* if table_owner is null, include all in search */
    if (@fktable_owner is null) select @fktable_owner = '%'
    if (@pktable_owner is null) select @pktable_owner = '%'

    set nocount on

    set transaction isolation level 1

    if (@startedInTransaction = 1)
        save transaction jdbc_keep_temptables_from_tx 

    if (@pktable_name is null) and (@fktable_name is null)
    begin
        /* If neither primary key nor foreign key table names given */
        /*
        ** 18043 'Primary key table name or foreign key table name
        ** or both must be given'
        */
        exec  sp_getmessage 18043, @msg output
        raiserror 18043 @msg
        return (1)
    end
        else
        begin
                if (substring(@pktable_name,1,1)= '#') or
                   (substring(@fktable_name,1,1)='#')  
                begin
                        /* We won't allow temptables here
                        ** 
                        ** Error 177: cannot create a temporary object (with
                        ** '#' as the first character name.
                        */
                        exec sp_getmessage 17676, @msg out
                        raiserror 17676 @msg
                        return(1)
                end
        end
    if @fktable_qualifier is not null
    begin
        if db_name() != @fktable_qualifier
        begin
            exec sp_getmessage 18039, @msg out
            raiserror 18039 @msg
            return (1)
        end
    end
    else
    begin
        /*
        ** Now make sure that foreign table qualifier is pointing to the
        ** current database in case it is not specified.
        */
        select @fktable_qualifier = db_name()
    end

    if @pktable_qualifier is not null
    begin
        if db_name() != @pktable_qualifier
        begin
            exec sp_getmessage 18039, @msg output
            raiserror 18039 @msg
            return (1)
        end
    end
    else
    begin
        /*
        ** Now make sure that primary table qualifier is pointing to the
        ** current database in case it is not specified.
        */
        select @pktable_qualifier = db_name()
    end

    create table #jpid (pid int, uid int, name varchar(32))
    create table #jfid (fid int, uid int, name varchar(32))

    if @pktable_name is not null
    begin
                select @export = 1
                if ((select count(*) from sysobjects 
            where name = @pktable_name
            and user_name(uid) like @pktable_owner ESCAPE '\'
            and type in ('S', 'U')) = 0)
        begin
            exec sp_getmessage 17674, @msg output
            raiserror 17674 @msg
            return (1)
        end
        
        insert into #jpid
        select id, uid, name
        from sysobjects
        where name = @pktable_name
        and user_name(uid) like @pktable_owner ESCAPE '\'
        and type in ('S', 'U')
    end
    else
    begin
        insert into #jpid
        select id, uid, name
        from sysobjects 
        where type in ('S', 'U')
        and user_name(uid) like @pktable_owner ESCAPE '\'
    end

    if @fktable_name is not null
    begin
        select @import = 1
        if ((select count(*)
            from sysobjects
            where name = @fktable_name
            and type in ('S', 'U')
            and user_name(uid) like @fktable_owner ESCAPE '\') = 0)
        begin
            exec sp_getmessage 17674, @msg output
            raiserror 17674 @msg
            return (1)
        end
        insert into #jfid
        select id, uid, name
            from sysobjects
            where name = @fktable_name
            and type in ('S', 'U')
            and user_name(uid) like @fktable_owner ESCAPE '\'
    end
    else
    begin
        insert into #jfid
        select id, uid, name
            from sysobjects where
            type in ('S', 'U')
            and user_name(uid) like @fktable_owner ESCAPE '\'
    end

    create table #jfkey_res( 
        PKTABLE_CAT        varchar(32) null,
        PKTABLE_SCHEM      varchar(32) null,
        PKTABLE_NAME       varchar(257) null,
        PKCOLUMN_NAME      varchar(257) null,
        FKTABLE_CAT        varchar(32) null,
        FKTABLE_SCHEM      varchar(32) null,
        FKTABLE_NAME       varchar(257) null,
        FKCOLUMN_NAME      varchar(257) null,
        KEY_SEQ            smallint,
        UPDATE_RULE        smallint,
        DELETE_RULE        smallint,
        FK_NAME            varchar(257),
        PK_NAME            varchar(257) null)
    create table #jpkeys(seq int, keys varchar(32) null)
    create table #jfkeys(seq int, keys varchar(32) null)

    /*
    ** Since there are possibly multiple rows in sysreferences
    ** that describe foreign and primary key relationships among
    ** two tables, so we declare a cursor on the selection from
    ** sysreferences and process the output at row by row basis.
    */

    declare jcurs_sysreferences cursor
        for
        select tableid, reftabid, constrid, keycnt,
            fokey1, fokey2, fokey3, fokey4, fokey5, fokey6, fokey7, fokey8,
            fokey9, fokey10, fokey11, fokey12, fokey13, fokey14, fokey15,
            fokey16, refkey1, refkey2, refkey3, refkey4, refkey5,
            refkey6, refkey7, refkey8, refkey9, refkey10, refkey11,
            refkey12, refkey13, refkey14, refkey15, refkey16
            from sysreferences
            where tableid in (
                    select fid from #jfid)
            and reftabid in (
                    select pid from #jpid)
            and frgndbname is NULL and pmrydbname is NULL
            for read only

    open  jcurs_sysreferences

    fetch  jcurs_sysreferences into @ftabid, @ptabid, @constrid, @keycnt,
        @fokey1, @fokey2, @fokey3,  @fokey4, @fokey5, @fokey6, @fokey7, @fokey8,
        @fokey9, @fokey10, @fokey11, @fokey12, @fokey13, @fokey14, @fokey15,
        @fokey16, @refkey1, @refkey2, @refkey3, @refkey4, @refkey5, @refkey6,
        @refkey7, @refkey8, @refkey9, @refkey10, @refkey11, @refkey12,
        @refkey13, @refkey14, @refkey15, @refkey16

    while (@@sqlstatus = 0)
    begin
        /*
        ** For each row of sysreferences which describes a foreign-
        ** primary key relationship, do the following.
        */
        
        /*
        ** First store the column names that belong to primary keys
        ** in table #pkeys for later retrieval.
        */
        
        delete #jpkeys
        insert #jpkeys values(1, col_name(@ptabid,@refkey1))
        insert #jpkeys values(2, col_name(@ptabid,@refkey2))
        insert #jpkeys values(3, col_name(@ptabid,@refkey3))
        insert #jpkeys values(4, col_name(@ptabid,@refkey4))
        insert #jpkeys values(5, col_name(@ptabid,@refkey5))
        insert #jpkeys values(6, col_name(@ptabid,@refkey6))
        insert #jpkeys values(7, col_name(@ptabid,@refkey7))
        insert #jpkeys values(8, col_name(@ptabid,@refkey8))
        insert #jpkeys values(9, col_name(@ptabid,@refkey9))
        insert #jpkeys values(10, col_name(@ptabid,@refkey10))
        insert #jpkeys values(11, col_name(@ptabid,@refkey11))
        insert #jpkeys values(12, col_name(@ptabid,@refkey12))
        insert #jpkeys values(13, col_name(@ptabid,@refkey13))
        insert #jpkeys values(14, col_name(@ptabid,@refkey14))
        insert #jpkeys values(15, col_name(@ptabid,@refkey15))
        insert #jpkeys values(16, col_name(@ptabid,@refkey16))
        
        /*
        ** Second store the column names that belong to foreign keys
        ** in table #jfkeys for later retrieval.
        */
        
        delete #jfkeys
        insert #jfkeys values(1, col_name(@ftabid,@fokey1))
        insert #jfkeys values(2, col_name(@ftabid,@fokey2))
        insert #jfkeys values(3, col_name(@ftabid,@fokey3))
        insert #jfkeys values(4, col_name(@ftabid,@fokey4))
        insert #jfkeys values(5, col_name(@ftabid,@fokey5))
        insert #jfkeys values(6, col_name(@ftabid,@fokey6))
        insert #jfkeys values(7, col_name(@ftabid,@fokey7))
        insert #jfkeys values(8, col_name(@ftabid,@fokey8))
        insert #jfkeys values(9, col_name(@ftabid,@fokey9))
        insert #jfkeys values(10, col_name(@ftabid,@fokey10))
        insert #jfkeys values(11, col_name(@ftabid,@fokey11))
        insert #jfkeys values(12, col_name(@ftabid,@fokey12))
        insert #jfkeys values(13, col_name(@ftabid,@fokey13))
        insert #jfkeys values(14, col_name(@ftabid,@fokey14))
        insert #jfkeys values(15, col_name(@ftabid,@fokey15))
        insert #jfkeys values(16, col_name(@ftabid,@fokey16))
        
        /*
        ** For each column of the current foreign-primary key relation,
        ** create a row into result table: #jfkey_res.
        */
        
        select @i = 1
        while (@i <= @keycnt)
        begin
            insert into #jfkey_res
                select @pktable_qualifier,
                (select user_name(uid) from #jpid where 
                    pid = @ptabid),
                object_name(@ptabid), 
                (select keys from #jpkeys where seq = @i),
                    @fktable_qualifier,
                (select user_name(uid) from #jfid where 
                    fid = @ftabid),
                object_name(@ftabid),
                (select keys from #jfkeys where seq = @i), 
                @i, 1, 1,
                /*Foreign key name*/ 
                object_name(@constrid),
                /* Primary key name */
                (select name from sysindexes where id = @ftabid
                    and status > 2048 and status < 32768)
            select @i = @i + 1
        end
        
        /*
        ** Go to the next foreign-primary key relationship if any.
        */
        
        fetch  jcurs_sysreferences into @ftabid, @ptabid, @constrid, 
            @keycnt,@fokey1, @fokey2, @fokey3,  @fokey4, @fokey5, @fokey6, 
            @fokey7, @fokey8, @fokey9, @fokey10, @fokey11, @fokey12, 
            @fokey13, @fokey14, @fokey15, @fokey16, @refkey1, @refkey2, 
            @refkey3, @refkey4, @refkey5, @refkey6, @refkey7, @refkey8, 
            @refkey9, @refkey10, @refkey11, @refkey12, @refkey13, @refkey14,
            @refkey15, @refkey16
    end

    close jcurs_sysreferences
    deallocate cursor jcurs_sysreferences

    /*
    ** Everything is now in the result table #jfkey_res, so go ahead
    ** and select from the table now.
    */
    if (@export = 1) and (@import = 0)
    begin
       select  PKTABLE_CAT,
               PKTABLE_SCHEM,
               PKTABLE_NAME,
               PKCOLUMN_NAME,
               FKTABLE_CAT, 
               FKTABLE_SCHEM, 
               FKTABLE_NAME, 
               FKCOLUMN_NAME,
               KEY_SEQ, 
               UPDATE_RULE, 
               DELETE_RULE,
               FK_NAME,
               PK_NAME, 
               @notDeferrable  as DEFERRABILITY
      from #jfkey_res 
      where PKTABLE_SCHEM like @pktable_owner ESCAPE '\'
      order by FKTABLE_CAT,FKTABLE_SCHEM,FKTABLE_NAME,KEY_SEQ
    end

    if (@export = 0) and (@import = 1)
    begin
        select  PKTABLE_CAT,
                PKTABLE_SCHEM,
                PKTABLE_NAME,
                PKCOLUMN_NAME,
                FKTABLE_CAT, 
                FKTABLE_SCHEM, 
                FKTABLE_NAME, 
                FKCOLUMN_NAME,
                KEY_SEQ, 
                UPDATE_RULE, 
                DELETE_RULE,
                FK_NAME,
                PK_NAME, 
                @notDeferrable  as DEFERRABILITY
        from #jfkey_res 
        where FKTABLE_SCHEM like @fktable_owner ESCAPE '\'
          order by PKTABLE_CAT,PKTABLE_SCHEM,PKTABLE_NAME,KEY_SEQ
    end

    if (@export = 1) and (@import = 1)
    begin
        select  PKTABLE_CAT,
                PKTABLE_SCHEM,
                PKTABLE_NAME,
                PKCOLUMN_NAME,
                FKTABLE_CAT, 
                FKTABLE_SCHEM, 
                FKTABLE_NAME, 
                FKCOLUMN_NAME,
                KEY_SEQ, 
                UPDATE_RULE, 
                DELETE_RULE,
                FK_NAME,
                PK_NAME, 
                @notDeferrable  as DEFERRABILITY
        from #jfkey_res 
        where PKTABLE_SCHEM like @pktable_owner ESCAPE '\'
        and FKTABLE_SCHEM like @fktable_owner ESCAPE '\'
          order by FKTABLE_CAT,FKTABLE_SCHEM,FKTABLE_NAME,KEY_SEQ
    end

    if (@startedInTransaction = 1)
        rollback transaction jdbc_keep_temptables_from_tx 


go

exec sp_procxmode 'sp_jdbc_fkeys', 'anymode'
go

/*
**  End of sp_jdbc_fkeys
*/


/* 
**  sp_jdbc_exportkey
*/


/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects where name =
	'sp_jdbc_exportkey')
	begin
		drop procedure sp_jdbc_exportkey
	end
go
/** SECTION END: CLEANUP **/


CREATE PROCEDURE sp_jdbc_exportkey (
				 @table_qualifier	varchar(32 ) = null,
				 @table_owner		varchar(32 ) = null,
				 @table_name		varchar(255))
as
	/* this will make sure that all rows are sent even if
	** the client "set rowcount" is differect
	*/

	set rowcount 0

	exec sp_jdbc_fkeys 
		@table_name, @table_owner, @table_qualifier, NULL, NULL, NULL
go
exec sp_procxmode 'sp_jdbc_exportkey', 'anymode'
go

grant execute on sp_jdbc_exportkey to public
go

/* 
**  End of sp_jdbc_exportkey
*/


/* 
** sp_jdbc_importkey
*/

/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects where name =
	'sp_jdbc_importkey')
	begin
		drop procedure sp_jdbc_importkey
	end
go
/** SECTION END: CLEANUP **/

CREATE PROCEDURE sp_jdbc_importkey (
				 @table_qualifier	varchar(32 ) = null,
				 @table_owner		varchar(32 ) = null,
				 @table_name		varchar(255))
as

	/* this will make sure that all rows are sent even if
	** the client "set rowcount" is differect
	*/

	set rowcount 0

	exec sp_jdbc_fkeys
		NULL, NULL, NULL, @table_name, @table_owner, @table_qualifier
go

exec sp_procxmode 'sp_jdbc_importkey', 'anymode'
go

grant execute on sp_jdbc_importkey to public
go

/* 
** End of sp_jdbc_importkey
*/



/*
**  sp_jdbc_getcrossreferences
*/


/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects
		where name = 'sp_jdbc_getcrossreferences')
begin
	drop procedure sp_jdbc_getcrossreferences
end
go
/** SECTION END: CLEANUP **/


CREATE PROCEDURE sp_jdbc_getcrossreferences
			   @pktable_qualifier	varchar(32 ) = null,
			   @pktable_owner	varchar(32 ) = null,
			   @pktable_name	varchar(255),
			   @fktable_qualifier	varchar(32 ) = null ,
			   @fktable_owner	varchar(32 ) = null,
			   @fktable_name	varchar(255)
as
	/* this will make sure that all rows are sent even if
	** the client "set rowcount" is differect
	*/

	set rowcount 0


	exec sp_jdbc_fkeys 
		@pktable_name, @pktable_owner, @pktable_qualifier,
		@fktable_name, @fktable_owner, @fktable_qualifier
go

exec sp_procxmode 'sp_jdbc_getcrossreferences', 'anymode'
go
grant execute on sp_jdbc_getcrossreferences to public
go
commit
go
dump transaction sybsystemprocs with truncate_only 
go

/*
**  End of sp_jdbc_getcrossreferences
*/


/* 
**  sp_jdbc_getschemas
*/

/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go
/*Stored procedure to support BCP implementation ADDPOINT_BULK_INSERT*/
IF EXISTS (SELECT * FROM sysobjects WHERE name = 'sp_drv_bcpmetadata')
BEGIN
    DROP PROCEDURE sp_drv_bcpmetadata
END
GO


CREATE PROCEDURE sp_drv_bcpmetadata(@table_name VARCHAR(771),
                                    @table_owner VARCHAR(32) = NULL,
                                    @table_qualifier VARCHAR(32) = NULL)
AS
    DECLARE @use_table_qualifier INT
    DECLARE @table_id            INT
    DECLARE @table_lock_scheme   INT
    DECLARE @dbid                INT
    DECLARE @number_of_columns   INT
    DECLARE @allow_wide_dol      INT
    DECLARE @max_rowlen          INT
    DECLARE @table_compressed    INT

    /*This will make sure that we will not emit any 
    **done count tokens associated with setting a variable*/
    SET NOCOUNT ON
    
    /*this will make sure that all rows are sent even if
    **the client 'set rowcount' is differect*/
    SET ROWCOUNT 0
    
    IF CHARINDEX('#', @table_name) = 1
    BEGIN
        SELECT @table_qualifier = DB_NAME(@@tempdbid)
    END
    ELSE IF @table_qualifier IS NULL
    BEGIN
        SELECT @table_qualifier = DB_NAME()
    END
    
    SELECT @dbid = DB_ID(@table_qualifier)
    SELECT @use_table_qualifier = CASE DB_NAME() WHEN @table_qualifier THEN 0 ELSE 1 END
    SELECT @table_id = OBJECT_ID(@table_qualifier + '.' + @table_owner + '.' + @table_name)
    SELECT @allow_wide_dol = CASE status4 & 524288 WHEN 0 THEN 0 ELSE 1 END FROM master.dbo.sysdatabases WHERE dbid = @dbid

    IF @use_table_qualifier = 1
    BEGIN
        DECLARE @command VARCHAR(16384)
        SELECT @command = 'SELECT @table_lock_scheme = CASE WHEN sysstat2 & 57344 IN (16384, 32768) THEN 1 ELSE 0 END FROM ' + @table_qualifier + '..sysobjects WHERE id = @table_id'
        EXECUTE(@command)
        SELECT @command = 'SELECT @max_rowlen = maxlen FROM ' + @table_qualifier + '..sysindexes WHERE id = @table_id AND indid IN (0, 1)'
        EXECUTE(@command)
        SELECT @command = 'SELECT @table_compressed = CASE WHEN (sysstat3 & 16384 = 16384) THEN 1 ELSE 0 END FROM ' + @table_qualifier + '..sysobjects WHERE id = @table_id'
        EXECUTE(@command)
        SELECT @command = '
        SELECT @number_of_columns = MAX(value) FROM
            (SELECT COUNT(*) AS value FROM ' + @table_qualifier + '..syscolumns WHERE id = @table_id
            UNION SELECT NODC_GET_DROPCOL_ATTR(@dbid, @table_id, 1, ''numcols'') AS value
            ) AS column_counts'
        EXECUTE(@command)
        SELECT @number_of_columns, @table_qualifier
              ,@table_compressed
        SELECT @command = '
        SELECT
            COLID,
            COLUMN_NAME,
            DATA_TYPE,
            USERTYPE,
            COLUMN_SIZE,
            PRECISION,
            SCALE,
            NULLABLE,
            COLUMN_DEF,
            IDENTITY_COL,
            @table_lock_scheme AS TABLE_LOCK_SCHEME,
            @@maxpagesize AS PAGESIZE
            ,@allow_wide_dol AS ALLOW_WIDE_DOL,
            @max_rowlen AS MAX_ROWLEN,
            COL_OFFSET,
            TRUNCATE_VARBINARY_ZEROS,
            IS_INROWLOB,
            INROWLOB_LEN,
            ENCRYPTED_COL
        FROM
            (SELECT 
                colid AS COLID, 
                name AS COLUMN_NAME,
                DATA_TYPE = 
                    CASE type
                        WHEN 189 THEN 187
                        WHEN 190 THEN 188
                        ELSE type
                    END,
                usertype AS USERTYPE,
                length AS COLUMN_SIZE,
                ISNULL(prec, 0) AS PRECISION,
                ISNULL(scale, 0) AS SCALE,
                NULLABLE = 
                    CASE status & 0x08
                        WHEN 0x08 THEN 1
                        ELSE 0
                    END,
                ' + @table_qualifier + '.dbo.sp_drv_getcomment(cdefault) AS COLUMN_DEF,
                IDENTITY_COL = 
                    CASE status & 0x80
                        WHEN 0x80 THEN 1
                        ELSE 0
                    END
                ,offset AS COL_OFFSET,
                TRUNCATE_VARBINARY_ZEROS = 
                    CASE 
                        WHEN (status2 & 2097152) > 0 THEN 0
                        ELSE 1
                    END,
                IS_INROWLOB = 
                    CASE 
                        WHEN (status2 & 262144) > 0 THEN 1
                        ELSE 0
                    END,
                inrowlen AS INROWLOB_LEN,
                ENCRYPTED_COL =
                    CASE WHEN status2 & 0x80 > 0
                        THEN 1
                        ELSE 0
                    END
            FROM 
                ' + @table_qualifier + '..syscolumns 
            WHERE 
                id = @table_id
            UNION
            SELECT 
                colid AS COLID, 
                '' '' AS COLUMN_NAME,
                DATA_TYPE = 
                    CASE type
                        WHEN 135 THEN 225
                        WHEN 155 THEN 225
                        WHEN 189 THEN 187
                        WHEN 190 THEN 188
                        ELSE type
                    END,
                USERTYPE = 
                    CASE type
                        WHEN 135 THEN 34
                        WHEN 155 THEN 35
                        ELSE 0
                    END,
                length AS COLUMN_SIZE,
                precision AS PRECISION,
                0 AS SCALE,
                nullable AS NULLABLE,
                COLUMN_DEF = 
                    CASE 
                        WHEN nullable = 1 THEN NULL
                        WHEN type IN (49, 58, 123) THEN ''2000-01-01''  --TDS_DATE, TDS_SHORTDATE, TDS_DATEN
                        WHEN type IN (51, 147, 188, 190) THEN ''01:01:01'' --TDS_TIME, TDS_TIMEN, TDS_BIGTIMEN, TDS_BIGTIMEN(ALT)
                        WHEN type IN (61, 111, 187, 189) THEN ''2000-01-01 01:01:01'' --TDS_DATETIME, TDS_DATETIMN, TDS_BIGDATETIMEN, TDS_BIGDATETIMEN(ALT)
                        ELSE ''0''
                        END,
                0 AS IDENTITY_COL,
                offset AS COL_OFFSET,
                0 AS TRUNCATE_VARBINARY_ZEROS,
                0 AS IS_INROWLOB,
                0 AS INROWLOB_LEN,
                0 AS ENCRYPTED_COL
            FROM 
                (SELECT  
                    colid,
                    offset,
                    nullable = 
                        CASE 
                            WHEN offset < 0 THEN 1
                            ELSE 0
                        END,
                    NODC_GET_DROPCOL_ATTR(@dbid, @table_id, colid, ''type'') AS type,
                    NODC_GET_DROPCOL_ATTR(@dbid, @table_id, colid, ''length'') AS length,
                    NODC_GET_DROPCOL_ATTR(@dbid, @table_id, colid, ''precision'') AS precision
                FROM 
                    (SELECT 
                        number AS colid,
                        NODC_GET_DROPCOL_ATTR(@dbid, @table_id, number, ''offset'') AS offset
                    FROM
                        master.dbo.spt_values 
                    WHERE 
                        type = ''P'' AND
                        number BETWEEN 1 AND @number_of_columns AND 
                        number NOT IN (SELECT colid FROM ' + @table_qualifier + '..syscolumns WHERE id = @table_id)) AS COLUMNOFFSETS) AS DROPEDCOLUMNS
            ) AS COLUMNS
            
        ORDER BY
            COLID'
        EXECUTE(@command)
    END
    ELSE
    BEGIN
        SELECT @table_lock_scheme = CASE WHEN sysstat2 & 57344 IN (16384, 32768) THEN 1 ELSE 0 END FROM sysobjects WHERE id = @table_id
        SELECT @max_rowlen = maxlen FROM sysindexes WHERE id = @table_id AND indid IN (0, 1)
        SELECT @table_compressed = CASE WHEN (sysstat3 & 16384 = 16384) THEN 1 ELSE 0 END FROM sysobjects WHERE id = @table_id
        SELECT @number_of_columns = MAX(value) FROM
            (SELECT COUNT(*) AS value FROM syscolumns WHERE id = @table_id
            UNION SELECT NODC_GET_DROPCOL_ATTR(@dbid, @table_id, 1, 'numcols') AS value
            ) AS column_counts
        SELECT @number_of_columns, @table_qualifier
              ,@table_compressed
        SELECT
            COLID,
            COLUMN_NAME,
            DATA_TYPE,
            USERTYPE,
            COLUMN_SIZE,
            PRECISION,
            SCALE,
            NULLABLE,
            COLUMN_DEF,
            IDENTITY_COL,
            @table_lock_scheme AS TABLE_LOCK_SCHEME,
            @@maxpagesize AS PAGESIZE
            ,@allow_wide_dol AS ALLOW_WIDE_DOL,
            @max_rowlen AS MAX_ROWLEN,
            COL_OFFSET,
            TRUNCATE_VARBINARY_ZEROS,
            IS_INROWLOB,
            INROWLOB_LEN,
            ENCRYPTED_COL
        FROM
            (SELECT 
                colid AS COLID, 
                name AS COLUMN_NAME,
                DATA_TYPE = 
                    CASE type
                        WHEN 189 THEN 187
                        WHEN 190 THEN 188
                        ELSE type
                    END,
                usertype AS USERTYPE,
                length AS COLUMN_SIZE,
                ISNULL(prec, 0) AS PRECISION,
                ISNULL(scale, 0) AS SCALE,
                NULLABLE = 
                    CASE status & 0x08
                        WHEN 0x08 THEN 1
                        ELSE 0
                    END,
                dbo.sp_drv_getcomment(cdefault) AS COLUMN_DEF,
                IDENTITY_COL = 
                    CASE status & 0x80
                        WHEN 0x80 THEN 1
                        ELSE 0
                    END
                ,offset AS COL_OFFSET,
                TRUNCATE_VARBINARY_ZEROS = 
                    CASE 
                        WHEN (status2 & 2097152) > 0 THEN 0
                        ELSE 1
                    END,
                IS_INROWLOB = 
                    CASE 
                        WHEN (status2 & 262144) > 0 THEN 1
                        ELSE 0
                    END,
                inrowlen AS INROWLOB_LEN,
                ENCRYPTED_COL =
                    CASE WHEN status2 & 0x80 > 0
                        THEN 1
                        ELSE 0
                    END    
            FROM 
                syscolumns 
            WHERE 
                id = @table_id
            UNION
            SELECT 
                colid AS COLID, 
                ' ' AS COLUMN_NAME,
                DATA_TYPE = 
                    CASE type
                        WHEN 135 THEN 225
                        WHEN 155 THEN 225
                        WHEN 189 THEN 187
                        WHEN 190 THEN 188
                        ELSE type
                    END,
                USERTYPE = 
                    CASE type
                        WHEN 135 THEN 34
                        WHEN 155 THEN 35
                        ELSE 0
                    END,
                length AS COLUMN_SIZE,
                precision AS PRECISION,
                0 AS SCALE,
                nullable AS NULLABLE,
                COLUMN_DEF = 
                    CASE 
                        WHEN nullable = 1 THEN NULL
                        WHEN type IN (49, 58, 123) THEN '2000-01-01'   --TDS_DATE, TDS_SHORTDATE, TDS_DATEN
                        WHEN type IN (51, 147, 188, 190) THEN '01:01:01' --TDS_TIME, TDS_TIMEN, TDS_BIGTIMEN, TDS_BIGTIMEN(ALT)
                        WHEN type IN (61, 111, 187, 189) THEN '2000-01-01 01:01:01'  --TDS_DATETIME, TDS_DATETIMN, TDS_BIGDATETIMEN, TDS_BIGDATETIMEN(ALT)
                        ELSE '0'
                        END,
                0 AS IDENTITY_COL,
                offset AS COL_OFFSET,
                0 AS TRUNCATE_VARBINARY_ZEROS,
                0 AS IS_INROWLOB,
                0 AS INROWLOB_LEN,
                0 AS ENCRYPTED_COL
            FROM 
                (SELECT  
                    colid,
                    offset,
                    nullable = 
                        CASE 
                            WHEN offset < 0 THEN 1
                            ELSE 0
                        END,
                    NODC_GET_DROPCOL_ATTR(@dbid, @table_id, colid, 'type') AS type,
                    NODC_GET_DROPCOL_ATTR(@dbid, @table_id, colid, 'length') AS length,
                    NODC_GET_DROPCOL_ATTR(@dbid, @table_id, colid, 'precision') AS precision
                FROM 
                    (SELECT 
                        number AS colid,
                        NODC_GET_DROPCOL_ATTR(@dbid, @table_id, number, 'offset') AS offset
                    FROM
                        master.dbo.spt_values 
                    WHERE 
                        type = 'P' AND
                        number BETWEEN 1 AND @number_of_columns AND 
                        number NOT IN (SELECT colid FROM syscolumns WHERE id = @table_id)) AS COLUMNOFFSETS) AS DROPEDCOLUMNS
            ) AS COLUMNS
            
        ORDER BY
            COLID
    END
    RETURN(0)
GO
EXECUTE sp_procxmode 'sp_drv_bcpmetadata', 'anymode'
GO
GRANT EXECUTE ON sp_drv_bcpmetadata TO PUBLIC
GO
DUMP TRANSACTION master WITH TRUNCATE_ONLY
GO
DUMP TRANSACTION sybsystemprocs WITH TRUNCATE_ONLY
GO

/*Stored procedure to support CTS test suite  ADDPOINT_GETSCHEMAS_CTS*/
/*
**  sp_jdbc_getschemas_cts
*/

if exists (select * from sysobjects where name = 'sp_jdbc_getschemas_cts')
    begin
        drop procedure sp_jdbc_getschemas_cts
    end
go
/** SECTION END: CLEANUP **/


CREATE PROCEDURE sp_jdbc_getschemas_cts
as
declare @schemaname varchar(32 )
declare @startedInTransaction       bit

    if @@trancount = 0
    begin
        set chained off
    end
    /* check if we're in a transaction, before we try any select statements */
    if (@@trancount > 0)
      select @startedInTransaction = 1
    else
      select @startedInTransaction = 0  

    set transaction isolation level 1

    if (@startedInTransaction = 1)
       save transaction jdbc_keep_temptables_from_tx

    /* this will make sure that all rows are sent even if
    ** the client "set rowcount" is differect
    */

    set rowcount 0

    create table #tmpschemas
    ( TABLE_SCHEM  varchar (32) null)

    DECLARE jcurs_getschemas CURSOR
    FOR
    select name from sysusers where suid >= -2
    FOR READ ONLY
    OPEN  jcurs_getschemas
    FETCH jcurs_getschemas INTO @schemaname
    while (@@sqlstatus = 0)
    begin
        insert into #tmpschemas values(@schemaname)
        FETCH jcurs_getschemas INTO @schemaname
    end

    close jcurs_getschemas
    deallocate cursor jcurs_getschemas

    select TABLE_SCHEM  from #tmpschemas order by TABLE_SCHEM
    drop table #tmpschemas
    if (@startedInTransaction = 1)
        rollback transaction jdbc_keep_temptables_from_tx
go

exec sp_procxmode 'sp_jdbc_getschemas_cts', 'anymode'
go

grant execute on sp_jdbc_getschemas_cts to public
go

/*
**  End of sp_jdbc_getschemas
*/

if exists (select * from sysobjects where name = 'sp_jdbc_getschemas')
	begin
		drop procedure sp_jdbc_getschemas
	end
go
/** SECTION END: CLEANUP **/


CREATE PROCEDURE sp_jdbc_getschemas 
@sp_qualifier            varchar(32 ) = null,     /* stored procedure qualifier*/
@sp_owner                varchar(32 ) = null      /* stored procedure owner */
as

	if @@trancount = 0
	begin
		set chained off
	end

	if @sp_owner is null
		select @sp_owner = '%'

	if @sp_qualifier is null
		select @sp_qualifier = db_name()

	set transaction isolation level 1
	/* this will make sure that all rows are sent even if
	** the client "set rowcount" is differect
	*/

	set rowcount 0

	select TABLE_SCHEM=name, TABLE_CATALOG=@sp_qualifier from  
			sysusers where suid >= -2 and name like @sp_owner order by name
go

exec sp_procxmode 'sp_jdbc_getschemas', 'anymode'
go

grant execute on sp_jdbc_getschemas to public
go

commit 
go
dump transaction sybsystemprocs with truncate_only 
go

/* 
**  End of sp_jdbc_getschemas
*/

/* Don't delete the following line. It is the checkpoint for sed */
/* Server dependent stored procedure add here ad ADDPOINT_DRV_GETPRIVILEGE */
IF EXISTS (SELECT * FROM sysobjects WHERE name = 'sp_drv_anyprivilege')
BEGIN
    DROP FUNCTION sp_drv_anyprivilege
END
GO

CREATE FUNCTION sp_drv_anyprivilege(@tableprivilege INT) 
RETURNS INT
AS
BEGIN
    DECLARE @any_privilege INT
    IF (select value from master.dbo.sysconfigures where config = 526) = 0
       SELECT @any_privilege = @tableprivilege
    ELSE IF @tableprivilege = 83
        SELECT @any_privilege = 81
    ELSE IF @tableprivilege = 84
        SELECT @any_privilege = 82
    ELSE IF @tableprivilege = 151
        SELECT @any_privilege = 129
    ELSE IF @tableprivilege = 193
        SELECT @any_privilege = 134
    ELSE IF @tableprivilege = 195
        SELECT @any_privilege = 85
    ELSE IF @tableprivilege = 196
        SELECT @any_privilege = 66
    ELSE IF @tableprivilege = 197
        SELECT @any_privilege = 145
    ELSE IF @tableprivilege = 320
        SELECT @any_privilege = 140
    ELSE IF @tableprivilege = 353
        SELECT @any_privilege = 153
    ELSE IF @tableprivilege = 368
        SELECT @any_privilege = 138
    ELSE IF @tableprivilege = 282
        SELECT @any_privilege = 99
    ELSE IF @tableprivilege = 326
        SELECT @any_privilege = 99
    ELSE
        SELECT @any_privilege = 0
    RETURN @any_privilege
END
GO
GRANT EXECUTE ON sp_drv_anyprivilege TO public
GO
DUMP TRANSACTION master WITH truncate_only
GO
DUMP TRANSACTION sybsystemprocs WITH truncate_only
GO


IF EXISTS (SELECT *
           FROM sysobjects
           WHERE sysstat & 7 = 4
               AND name = 'sp_drv_getcolumnprivileges')
BEGIN
    DROP PROCEDURE sp_drv_getcolumnprivileges
END
GO

/* Sccsid = '%Z% generic/sproc/src/%M% %I% %G%' */

CREATE PROCEDURE sp_drv_getcolumnprivileges(@table_name VARCHAR(771),
                                            @table_owner VARCHAR(32) = NULL,
                                            @table_qualifier VARCHAR(32) = NULL,
                                            @column_name VARCHAR(771) = NULL)

AS 
    /*Check if the table is a temporary table and we are in the temp db*/
    IF (@table_name LIKE '#%' AND DB_NAME() != db_name(tempdb_id()))
    BEGIN
        /*17676, 'This may be a temporary object. Please execute procedure from your temporary database.'*/
        RAISERROR 17676
        RETURN (1)
    END

    /*Check that the database of the table is the current database*/
    IF (@table_qualifier IS NULL) OR (@table_qualifier = '')
        SELECT @table_qualifier = DB_NAME()
    ELSE
    BEGIN
        IF DB_NAME() != @table_qualifier
        BEGIN
            /*18039, 'Table qualifier must be name of current database.'*/
            RAISERROR 18039
            RETURN (1)
        END
    END
    
    IF (@table_owner IS NULL) OR (@table_owner = '')
        SELECT @table_owner = '%'  
    
    IF (@column_name IS NULL) OR (@column_name = '')
        SELECT @column_name = '%'  

    SELECT DISTINCT
        TABLE_CAT,
        TABLE_SCHEM,
        TABLE_NAME,
        COLUMN_NAME,
        GRANTOR,
        GRANTEE,
        PRIVILEGE,
        IS_GRANTABLE,
        PREDICATE
    FROM
        --explicit grants/revokes
        (SELECT
            *
        FROM
            (SELECT 
                DB_NAME() AS TABLE_CAT,
                matchinggrantrecords.table_owner AS TABLE_SCHEM,
                matchinggrantrecords.table_name AS TABLE_NAME,
                matchinggrantrecords.column_name AS COLUMN_NAME,
                matchinggrantrecords.grantor AS GRANTOR,
                posiblegrants.grantee AS GRANTEE,
                PRIVILEGE = 
                    CASE 
                        WHEN posiblegrants.action = 83 THEN 'IDENTITY INSERT'
                        WHEN posiblegrants.action = 84 THEN 'IDENTITY UPDATE'
                        WHEN posiblegrants.action = 151 THEN 'REFERENCES'
                        WHEN posiblegrants.action = 193 THEN 'SELECT'
                        WHEN posiblegrants.action = 195 THEN 'INSERT'
                        WHEN posiblegrants.action = 196 THEN 'DELETE'
                        WHEN posiblegrants.action = 197 THEN 'UPDATE'
                        WHEN posiblegrants.action = 282 THEN 'DELETE STATISTICS'
                        WHEN posiblegrants.action = 320 THEN 'TRUNCATE TABLE'
                        WHEN posiblegrants.action = 326 THEN 'UPDATE STATISTICS'
                        WHEN posiblegrants.action = 353 THEN 'DECRYPT'
                        WHEN posiblegrants.action = 368 THEN 'TRANSFER TABLE'
                        ELSE 'UNKNOWN'
                    END, 
                IS_GRANTABLE = 
                    CASE 
                        WHEN matchinggrantrecords.protecttype = 0 THEN 'YES'
                        ELSE 'NO'
                    END 
                ,dbo.sp_drv_getcomment(matchinggrantrecords.predid) AS PREDICATE
            FROM
                (SELECT
                    actions.action,
                    dbo.sp_drv_anyprivilege(actions.action) AS any_action,
                    userrecord.name AS grantee,
                    userrecord.suid,
                    userrecord.uid,
                    userrecord.gid
                FROM
                    --These are the only actions that are column specific with the exception of INSERT
                    --INSERT is included because it is explicitly listed as a column permission in the ODBC spec
                    --also it makes sense to make insert column specific.
                    --You might not want someone messing with defaults like timestamps on a business transaction
                    (SELECT 151 AS action
                    UNION
                    SELECT 193 AS action
                    UNION
                    SELECT 195 AS action
                    UNION
                    SELECT 197 AS action
                    UNION
                    SELECT 353 AS action) AS actions,
                    (SELECT
                        name,
                        suid,
                        uid,
                        gid
                    FROM
                        sysusers
                    WHERE
                        suid > -2) AS userrecord) AS posiblegrants
            INNER JOIN
                (SELECT 
                    tabletcolumns.table_id,
                    USER_NAME(subprotects.grantorid) AS grantor,
                    subprotects.granteeid,
                    subprotects.protecttype,
                    subprotects.action,
                    subprotects.columns,
                    subprotects.predid,
                    tabletcolumns.table_owner,
                    tabletcolumns.table_name,
                    tabletcolumns.column_name,
                    tabletcolumns.colid
                FROM
                    (SELECT 
                        protects.id AS table_id,
                        protects.grantor AS grantorid,
                        protects.uid AS granteeid,
                        protects.protecttype,
                        protects.action,
                        protects.predid,
                        protects.columns
                    FROM 
                        sysprotects AS protects
                    WHERE 
                        protects.protecttype < 2) AS subprotects
                INNER JOIN    
                    (SELECT 
                        objects.id AS table_id,
                        USER_NAME(objects.uid) AS table_owner,
                        objects.name AS table_name,
                        columns.name AS column_name,
                        columns.colid,
                        (columns.colid / 8) + 1 AS column_byte_index,
                        CONVERT(TINYINT, POWER(2, columns.colid % 8)) AS column_flag_index
                    FROM 
                        (SELECT
                            id,
                            uid,
                            name
                        FROM
                            sysobjects
                        WHERE
                            name LIKE @table_name ESCAPE '\' AND
                            USER_NAME(uid) LIKE @table_owner ESCAPE '\' AND
                            type IN ('S', 'U', 'V'))AS objects
                    INNER JOIN
                        (SELECT 
                            name, colid, id
                        FROM
                            syscolumns
                        WHERE
                            name LIKE @column_name ESCAPE '\') AS columns
                    ON
                        objects.id = columns.id) AS tabletcolumns
                ON
                    subprotects.table_id IN (tabletcolumns.table_id, 0) AND
                    (subprotects.columns = 0x01 OR
                        subprotects.columns IS NULL OR
                        SUBSTRING(subprotects.columns, tabletcolumns.column_byte_index, 1) & tabletcolumns.column_flag_index != 0)) AS matchinggrantrecords
            ON
                matchinggrantrecords.action IN (posiblegrants.action, posiblegrants.any_action) AND
                (posiblegrants.uid = matchinggrantrecords.granteeid OR
                    --uid NOT IN matching used records AND
                    (NOT EXISTS(SELECT sysprotects.uid FROM (SELECT uid, id, columns, action FROM sysprotects) AS sysprotects LEFT JOIN (SELECT id, colid FROM syscolumns) AS syscolumns ON sysprotects.id IN (syscolumns.id, 0) AND (sysprotects.columns = 0x01 OR sysprotects.columns IS NULL OR SUBSTRING(sysprotects.columns, (syscolumns.colid / 8) + 1, 1) & CONVERT(TINYINT, POWER(2, syscolumns.colid % 8)) != 0) WHERE sysprotects.uid = posiblegrants.uid AND sysprotects.action IN (posiblegrants.action, posiblegrants.any_action) AND sysprotects.id IN (matchinggrantrecords.table_id, 0) AND syscolumns.colid = matchinggrantrecords.colid) AND
                        --roleid IN matchinggrantrecords OR
                        (matchinggrantrecords.granteeid IN (SELECT sysusers.uid FROM master.dbo.sysloginroles AS sysloginroles LEFT JOIN master.dbo.syssrvroles AS syssrvroles ON sysloginroles.srid = syssrvroles.srid LEFT JOIN sysusers ON syssrvroles.name = sysusers.name WHERE sysloginroles.suid = posiblegrants.suid) OR
                            --roleid NOT IN matching used records AND
                            (NOT EXISTS(SELECT rolelist.suid FROM (SELECT sysprotects.uid, sysprotects.action, sysprotects.id, syscolumns.colid FROM sysprotects LEFT JOIN syscolumns ON sysprotects.id IN (syscolumns.id, 0) AND (sysprotects.columns = 0x01 OR sysprotects.columns IS NULL OR SUBSTRING(sysprotects.columns, (syscolumns.colid / 8) + 1, 1) & CONVERT(TINYINT, POWER(2, syscolumns.colid % 8)) != 0)) AS usedlist INNER JOIN (SELECT sysusers.uid, sysloginroles.suid FROM master.dbo.sysloginroles AS sysloginroles LEFT JOIN master.dbo.syssrvroles AS syssrvroles ON sysloginroles.srid = syssrvroles.srid LEFT JOIN sysusers ON syssrvroles.name = sysusers.name) AS rolelist ON usedlist.uid = rolelist.uid WHERE usedlist.action IN (posiblegrants.action, posiblegrants.any_action) AND usedlist.id IN (matchinggrantrecords.table_id, 0) AND rolelist.suid = posiblegrants.suid AND usedlist.colid = matchinggrantrecords.colid) AND
                                --gid IN matchinggrantrecords OR
                                (posiblegrants.gid = matchinggrantrecords.granteeid OR
                                    --gid NOT IN matching used records AND
                                    (NOT EXISTS(SELECT sysprotects.uid FROM (SELECT uid, id, columns, action FROM sysprotects) AS sysprotects LEFT JOIN (SELECT id, colid FROM syscolumns) AS syscolumns ON sysprotects.id IN (syscolumns.id, 0) AND (sysprotects.columns = 0x01 OR sysprotects.columns IS NULL OR SUBSTRING(sysprotects.columns, (syscolumns.colid / 8) + 1, 1) & CONVERT(TINYINT, POWER(2, syscolumns.colid % 8)) != 0) WHERE sysprotects.uid = posiblegrants.gid AND sysprotects.action IN (posiblegrants.action, posiblegrants.any_action) AND sysprotects.id IN (matchinggrantrecords.table_id, 0) AND syscolumns.colid = matchinggrantrecords.colid) AND
                                        --public in matchinggrantrecords
                                        0 = matchinggrantrecords.granteeid))))))) AS EXPLICITGRANTS
        UNION
        --implicit grants
        SELECT
            *
        FROM
            (SELECT 
                DB_NAME() AS TABLE_CAT,
                tabletcolumns.table_owner AS TABLE_SCHEM,
                tabletcolumns.table_name AS TABLE_NAME,
                tabletcolumns.column_name AS COLUMN_NAME,
                '_SYSTEM' AS GRANTOR,
                posiblegrants.grantee AS GRANTEE,
                PRIVILEGE = 
                    CASE 
                        WHEN posiblegrants.action = 83 THEN 'IDENTITY INSERT'
                        WHEN posiblegrants.action = 84 THEN 'IDENTITY UPDATE'
                        WHEN posiblegrants.action = 151 THEN 'REFERENCES'
                        WHEN posiblegrants.action = 193 THEN 'SELECT'
                        WHEN posiblegrants.action = 195 THEN 'INSERT'
                        WHEN posiblegrants.action = 196 THEN 'DELETE'
                        WHEN posiblegrants.action = 197 THEN 'UPDATE'
                        WHEN posiblegrants.action = 282 THEN 'DELETE STATISTICS'
                        WHEN posiblegrants.action = 320 THEN 'TRUNCATE TABLE'
                        WHEN posiblegrants.action = 326 THEN 'UPDATE STATISTICS'
                        WHEN posiblegrants.action = 353 THEN 'DECRYPT'
                        WHEN posiblegrants.action = 368 THEN 'TRANSFER TABLE'
                        ELSE 'UNKNOWN'
                    END, 
                'YES' AS IS_GRANTABLE 
                ,CONVERT(VARCHAR(255), NULL) AS PREDICATE 
            FROM
                (SELECT
                    actions.action,
                    dbo.sp_drv_anyprivilege(actions.action) AS any_action,
                    userrecord.name AS grantee,
                    userrecord.suid,
                    userrecord.uid,
                    userrecord.gid
                FROM
                    --These are the only actions that are column specific with the exception of INSERT
                    --INSERT is included because it is explicitly listed as a column permission in the ODBC spec
                    --also it makes sense to make insert column specific.
                    --You might not want someone messing with defaults like timestamps on a business transaction
                    (SELECT 151 AS action
                    UNION
                    SELECT 193 AS action
                    UNION
                    SELECT 195 AS action
                    UNION
                    SELECT 197 AS action
                    UNION
                    SELECT 353 AS action) AS actions,
                    (SELECT
                        name,
                        suid,
                        uid,
                        gid
                    FROM
                        sysusers 
                    WHERE
                        suid > -2) AS userrecord) AS posiblegrants
            INNER JOIN   
                (SELECT 
                    objects.id AS table_id,
                    objects.uid AS table_ownerid,
                    USER_NAME(objects.uid) AS table_owner,
                    objects.name AS table_name,
                    columns.name AS column_name,
                    columns.colid
                FROM 
                    sysobjects AS objects
                INNER JOIN
                    syscolumns AS columns
                ON
                    objects.id = columns.id
                WHERE 
                    objects.name LIKE @table_name ESCAPE '\' AND
                    USER_NAME(objects.uid) LIKE @table_owner ESCAPE '\' AND
                    columns.name LIKE @column_name ESCAPE '\' AND
                    objects.type IN ('S', 'U', 'V')) AS tabletcolumns
            ON
                posiblegrants.uid = tabletcolumns.table_ownerid AND
                --(the action is not decrypt OR implicit decrypt is enabled) AND
                (posiblegrants.action != 353 OR (SELECT value FROM master.dbo.sysconfigures WHERE config = 480) = 0) AND
                --uid NOT IN matching used records AND
                NOT EXISTS(SELECT sysprotects.uid FROM (SELECT uid, id, columns, action FROM sysprotects) AS sysprotects LEFT JOIN (SELECT id, colid FROM syscolumns) AS syscolumns ON sysprotects.id IN (syscolumns.id, 0) AND (sysprotects.columns = 0x01 OR sysprotects.columns IS NULL OR SUBSTRING(sysprotects.columns, (syscolumns.colid / 8) + 1, 1) & CONVERT(TINYINT, POWER(2, syscolumns.colid % 8)) != 0) WHERE sysprotects.uid = posiblegrants.uid AND sysprotects.action IN (posiblegrants.action, posiblegrants.any_action) AND sysprotects.id IN (tabletcolumns.table_id, 0) AND syscolumns.colid = tabletcolumns.colid) AND
                --roleid NOT IN matching used records AND
                NOT EXISTS(SELECT rolelist.suid FROM (SELECT sysprotects.uid, sysprotects.action, sysprotects.id, syscolumns.colid FROM sysprotects LEFT JOIN syscolumns ON sysprotects.id IN (syscolumns.id, 0) AND (sysprotects.columns = 0x01 OR sysprotects.columns IS NULL OR SUBSTRING(sysprotects.columns, (syscolumns.colid / 8) + 1, 1) & CONVERT(TINYINT, POWER(2, syscolumns.colid % 8)) != 0)) AS usedlist INNER JOIN (SELECT sysusers.uid, sysloginroles.suid FROM master.dbo.sysloginroles AS sysloginroles LEFT JOIN master.dbo.syssrvroles AS syssrvroles ON sysloginroles.srid = syssrvroles.srid LEFT JOIN sysusers ON syssrvroles.name = sysusers.name) AS rolelist ON usedlist.uid = rolelist.uid WHERE usedlist.action IN (posiblegrants.action, posiblegrants.any_action) AND usedlist.id IN (tabletcolumns.table_id, 0) AND rolelist.suid = posiblegrants.suid AND usedlist.colid = tabletcolumns.colid) AND
                --gid NOT IN matching used records AND
                NOT EXISTS(SELECT sysprotects.uid FROM (SELECT uid, id, columns, action FROM sysprotects) AS sysprotects LEFT JOIN (SELECT id, colid FROM syscolumns) AS syscolumns ON sysprotects.id IN (syscolumns.id, 0) AND (sysprotects.columns = 0x01 OR sysprotects.columns IS NULL OR SUBSTRING(sysprotects.columns, (syscolumns.colid / 8) + 1, 1) & CONVERT(TINYINT, POWER(2, syscolumns.colid % 8)) != 0) WHERE sysprotects.uid = posiblegrants.gid AND sysprotects.action IN (posiblegrants.action, posiblegrants.any_action) AND sysprotects.id IN (tabletcolumns.table_id, 0) AND syscolumns.colid = tabletcolumns.colid) AND
                --public NOT IN matchinggrantrecords
                NOT EXISTS(SELECT sysprotects.uid FROM (SELECT uid, id, columns, action FROM sysprotects WHERE uid = 0) AS sysprotects LEFT JOIN (SELECT id, colid FROM syscolumns) AS syscolumns ON sysprotects.id IN (syscolumns.id, 0) AND (sysprotects.columns = 0x01 OR sysprotects.columns IS NULL OR SUBSTRING(sysprotects.columns, (syscolumns.colid / 8) + 1, 1) & CONVERT(TINYINT, POWER(2, syscolumns.colid % 8)) != 0) WHERE sysprotects.action IN (posiblegrants.action, posiblegrants.any_action) AND sysprotects.id IN (tabletcolumns.table_id, 0) AND syscolumns.colid = tabletcolumns.colid)) AS IMPLICITGRANTS) AS COLUMNPRIVILEGES 
    ORDER BY 
        TABLE_CAT, 
        TABLE_SCHEM, 
        TABLE_NAME, 
        COLUMN_NAME, 
        PRIVILEGE, 
        GRANTEE

RETURN (0)
GO
EXECUTE sp_procxmode 'sp_drv_getcolumnprivileges', 'anymode'
GO
GRANT EXECUTE ON sp_drv_getcolumnprivileges TO public
GO
DUMP TRANSACTION master WITH truncate_only
GO
DUMP TRANSACTION sybsystemprocs WITH truncate_only
GO


IF EXISTS (SELECT *
           FROM sysobjects
           WHERE sysstat & 7 = 4
               AND name = 'sp_drv_gettableprivileges')
BEGIN
    DROP PROCEDURE sp_drv_gettableprivileges
END
GO

/* Sccsid = '%Z% generic/sproc/src/%M% %I% %G%' */

CREATE PROCEDURE sp_drv_gettableprivileges(@table_name VARCHAR(255),
                                           @table_owner VARCHAR(32) = NULL,
                                           @table_qualifier VARCHAR(32) = NULL)
AS
    /*Check if the table is a temporary table and we are in the temp db*/
    IF (@table_name LIKE '#%' AND DB_NAME() != db_name(tempdb_id()))
    BEGIN
        /*17676, 'This may be a temporary object. Please execute procedure from your temporary database.'*/
        RAISERROR 17676
        RETURN (1)
    END


    /*Check that the database of the table is the current database*/
    IF (@table_qualifier IS NULL) OR (@table_qualifier = '')
        SELECT @table_qualifier = DB_NAME()
    ELSE
    BEGIN
        IF DB_NAME() != @table_qualifier
        BEGIN
            /*18039, 'Table qualifier must be name of current database.'*/
            RAISERROR 18039
            RETURN (1)
        END
    END
    
    IF (@table_owner IS NULL) OR (@table_owner = '')
        SELECT @table_owner = '%'

    SELECT DISTINCT
        TABLE_CAT,
        TABLE_SCHEM,
        TABLE_NAME,
        GRANTOR,
        GRANTEE,
        PRIVILEGE,
        IS_GRANTABLE,
        PREDICATE
    FROM
        --explicit grants/revokes
        (SELECT
            *
        FROM
            (SELECT 
                DB_NAME() AS TABLE_CAT,
                matchinggrantrecords.table_owner AS TABLE_SCHEM,
                matchinggrantrecords.table_name AS TABLE_NAME,
                matchinggrantrecords.grantor AS GRANTOR,
                posiblegrants.grantee AS GRANTEE,
                PRIVILEGE = 
                    CASE 
                        WHEN posiblegrants.action = 83 THEN 'IDENTITY INSERT'
                        WHEN posiblegrants.action = 84 THEN 'IDENTITY UPDATE'
                        WHEN posiblegrants.action = 151 THEN 'REFERENCES'
                        WHEN posiblegrants.action = 193 THEN 'SELECT'
                        WHEN posiblegrants.action = 195 THEN 'INSERT'
                        WHEN posiblegrants.action = 196 THEN 'DELETE'
                        WHEN posiblegrants.action = 197 THEN 'UPDATE'
                        WHEN posiblegrants.action = 282 THEN 'DELETE STATISTICS'
                        WHEN posiblegrants.action = 320 THEN 'TRUNCATE TABLE'
                        WHEN posiblegrants.action = 326 THEN 'UPDATE STATISTICS'
                        WHEN posiblegrants.action = 353 THEN 'DECRYPT'
                        WHEN posiblegrants.action = 368 THEN 'TRANSFER TABLE'
                        ELSE 'UNKNOWN'
                    END, 
                IS_GRANTABLE = 
                    CASE 
                        WHEN matchinggrantrecords.protecttype = 0 AND 
                             (matchinggrantrecords.columns IS NULL OR
                              matchinggrantrecords.columns = 0x01) THEN 'YES'
                        ELSE 'NO'
                    END 
                ,dbo.sp_drv_getcomment(matchinggrantrecords.predid) AS PREDICATE 
            FROM
                (SELECT
                    actions.action,
                    dbo.sp_drv_anyprivilege(actions.action) AS any_action,
                    userrecord.name AS grantee,
                    userrecord.suid,
                    userrecord.uid,
                    userrecord.gid
                FROM
                    (SELECT 83 AS action
                    UNION
                    SELECT 84 AS action
                    UNION
                    SELECT 151 AS action
                    UNION
                    SELECT 193 AS action
                    UNION
                    SELECT 195 AS action
                    UNION
                    SELECT 196 AS action
                    UNION
                    SELECT 197 AS action
                    UNION
                    SELECT 282 AS action
                    UNION
                    SELECT 320 AS action
                    UNION
                    SELECT 326 AS action
                    UNION
                    SELECT 353 AS action
                    UNION
                    SELECT 368 AS action) AS actions,
                    (SELECT
                        name,
                        suid,
                        uid,
                        gid
                    FROM
                        sysusers 
                    WHERE
                        suid > -2) AS userrecord) AS posiblegrants
            INNER JOIN
                (SELECT 
                    tablets.table_id,
                    USER_NAME(subprotects.grantorid) AS grantor,
                    subprotects.granteeid,
                    subprotects.protecttype,
                    subprotects.action,
                    subprotects.columns,
                    subprotects.predid,
                    tablets.table_owner,
                    tablets.table_name
                FROM
                    (SELECT 
                        protects.id AS table_id,
                        protects.grantor AS grantorid,
                        protects.uid AS granteeid,
                        protects.protecttype,
                        protects.action,
                        protects.predid,
                        protects.columns
                    FROM 
                        sysprotects AS protects
                    WHERE 
                        protects.protecttype < 2) AS subprotects
                INNER JOIN    
                    (SELECT 
                        objects.id AS table_id,
                        USER_NAME(objects.uid) AS table_owner,
                        objects.name AS table_name
                    FROM 
                        sysobjects AS objects
                    WHERE 
                        objects.name LIKE @table_name ESCAPE '\' AND
                        USER_NAME(objects.uid) LIKE @table_owner ESCAPE '\' AND
                        objects.type IN ('S', 'U', 'V')) AS tablets
                ON
                    subprotects.table_id IN (tablets.table_id, 0)) AS matchinggrantrecords
            ON
                matchinggrantrecords.action IN (posiblegrants.action, posiblegrants.any_action) AND
                (posiblegrants.uid = matchinggrantrecords.granteeid OR
                    --uid NOT IN matching used records AND
                    (NOT EXISTS(SELECT sysprotects.uid FROM sysprotects WHERE sysprotects.uid = posiblegrants.uid AND sysprotects.action IN (posiblegrants.action, posiblegrants.any_action) AND sysprotects.id IN (matchinggrantrecords.table_id, 0)) AND
                        --roleid IN matchinggrantrecords OR
                        (EXISTS(SELECT sysusers.uid FROM (SELECT srid, suid FROM master.dbo.sysloginroles) AS sysloginroles LEFT JOIN (SELECT srid, name FROM master.dbo.syssrvroles) AS syssrvroles ON sysloginroles.srid = syssrvroles.srid LEFT JOIN sysusers ON syssrvroles.name = sysusers.name WHERE sysusers.uid = matchinggrantrecords.granteeid AND sysloginroles.suid = posiblegrants.suid) OR
                            --roleid NOT IN matching used records AND
                            (NOT EXISTS(SELECT rolelist.suid FROM (SELECT sysprotects.uid, sysprotects.action, sysprotects.id FROM sysprotects) AS usedlist INNER JOIN (SELECT sysusers.uid, sysloginroles.suid FROM master.dbo.sysloginroles AS sysloginroles LEFT JOIN master.dbo.syssrvroles AS syssrvroles ON sysloginroles.srid = syssrvroles.srid LEFT JOIN sysusers ON syssrvroles.name = sysusers.name) AS rolelist ON usedlist.uid = rolelist.uid WHERE usedlist.action IN (posiblegrants.action, posiblegrants.any_action) AND usedlist.id IN (matchinggrantrecords.table_id, 0) AND rolelist.suid = posiblegrants.suid) AND
                                --gid IN matchinggrantrecords OR
                                (posiblegrants.gid = matchinggrantrecords.granteeid OR
                                    --gid NOT IN matching used records AND
                                    (NOT EXISTS(SELECT sysprotects.uid FROM sysprotects WHERE sysprotects.uid = posiblegrants.gid AND sysprotects.action IN (posiblegrants.action, posiblegrants.any_action) AND sysprotects.id IN (matchinggrantrecords.table_id, 0)) AND
                                        --public in matchinggrantrecords
                                        0 = matchinggrantrecords.granteeid))))))) AS EXPLICITGRANTS
        UNION
        --implicit grants
        SELECT 
            * 
        FROM
            (SELECT 
                DB_NAME() AS TABLE_CAT,
                tablets.table_owner AS TABLE_SCHEM,
                tablets.table_name AS TABLE_NAME,
                '_SYSTEM' AS GRANTOR,
                posiblegrants.grantee AS GRANTEE,
                PRIVILEGE = 
                    CASE 
                        WHEN posiblegrants.action = 83 THEN 'IDENTITY INSERT'
                        WHEN posiblegrants.action = 84 THEN 'IDENTITY UPDATE'
                        WHEN posiblegrants.action = 151 THEN 'REFERENCES'
                        WHEN posiblegrants.action = 193 THEN 'SELECT'
                        WHEN posiblegrants.action = 195 THEN 'INSERT'
                        WHEN posiblegrants.action = 196 THEN 'DELETE'
                        WHEN posiblegrants.action = 197 THEN 'UPDATE'
                        WHEN posiblegrants.action = 282 THEN 'DELETE STATISTICS'
                        WHEN posiblegrants.action = 320 THEN 'TRUNCATE TABLE'
                        WHEN posiblegrants.action = 326 THEN 'UPDATE STATISTICS'
                        WHEN posiblegrants.action = 353 THEN 'DECRYPT'
                        WHEN posiblegrants.action = 368 THEN 'TRANSFER TABLE'
                        ELSE 'UNKNOWN'
                    END, 
                'YES' AS IS_GRANTABLE, 
                CONVERT(VARCHAR(255), NULL) AS PREDICATE 
            FROM
                (SELECT
                    actions.action,
                    dbo.sp_drv_anyprivilege(actions.action) AS any_action,
                    userrecord.name AS grantee,
                    userrecord.suid,
                    userrecord.uid,
                    userrecord.gid
                FROM
                    (SELECT 83 AS action
                    UNION
                    SELECT 84 AS action
                    UNION
                    SELECT 151 AS action
                    UNION
                    SELECT 193 AS action
                    UNION
                    SELECT 195 AS action
                    UNION
                    SELECT 196 AS action
                    UNION
                    SELECT 197 AS action
                    UNION
                    SELECT 282 AS action
                    UNION
                    SELECT 320 AS action
                    UNION
                    SELECT 326 AS action
                    UNION
                    SELECT 353 AS action
                    UNION
                    SELECT 368 AS action) AS actions,
                    (SELECT
                        name,
                        suid,
                        uid,
                        gid
                    FROM
                        sysusers 
                    WHERE
                        suid > -2) AS userrecord) AS posiblegrants
            INNER JOIN   
                (SELECT 
                    objects.id AS table_id,
                    objects.uid AS table_ownerid,
                    USER_NAME(objects.uid) AS table_owner,
                    objects.name AS table_name
                FROM 
                    sysobjects AS objects
                WHERE 
                    objects.name LIKE @table_name ESCAPE '\' AND
                    USER_NAME(objects.uid) LIKE @table_owner ESCAPE '\' AND
                    objects.type IN ('S', 'U', 'V')) AS tablets
            ON
                posiblegrants.uid = tablets.table_ownerid AND
                --(the action is not decrypt OR implicit decrypt is enabled) AND
                (posiblegrants.action != 353 OR (SELECT value FROM master.dbo.sysconfigures WHERE config = 480) = 0) AND
                --uid NOT IN matching used records AND
                NOT EXISTS(SELECT sysprotects.uid FROM sysprotects WHERE sysprotects.uid = posiblegrants.uid AND sysprotects.action IN (posiblegrants.action, posiblegrants.any_action) AND sysprotects.id IN (tablets.table_id, 0)) AND
                --roleid NOT IN matching used records AND
                NOT EXISTS(SELECT rolelist.suid FROM (SELECT sysprotects.uid, sysprotects.action, sysprotects.id FROM sysprotects) AS usedlist INNER JOIN (SELECT sysusers.uid, sysloginroles.suid FROM master.dbo.sysloginroles AS sysloginroles LEFT JOIN master.dbo.syssrvroles AS syssrvroles ON sysloginroles.srid = syssrvroles.srid LEFT JOIN sysusers ON syssrvroles.name = sysusers.name) AS rolelist ON usedlist.uid = rolelist.uid WHERE (usedlist.action = posiblegrants.action OR usedlist.action = posiblegrants.any_action) AND usedlist.id IN (tablets.table_id, 0) AND rolelist.suid = posiblegrants.suid) AND
                --gid NOT IN matching used records AND
                NOT EXISTS(SELECT sysprotects.uid FROM sysprotects WHERE sysprotects.uid = posiblegrants.gid AND sysprotects.action IN (posiblegrants.action, posiblegrants.any_action) AND sysprotects.id IN (tablets.table_id, 0)) AND
                --public NOT IN matchinggrantrecords
                NOT EXISTS(SELECT sysprotects.uid FROM sysprotects WHERE sysprotects.uid = 0 AND sysprotects.action IN (posiblegrants.action, posiblegrants.any_action) AND sysprotects.id IN (tablets.table_id, 0))) AS IMPLICITGRANTS) AS TABLEPRIVILEGES 
    ORDER BY 
        TABLE_CAT, 
        TABLE_SCHEM, 
        TABLE_NAME, 
        PRIVILEGE, 
        GRANTEE

RETURN (0)
GO
EXECUTE sp_procxmode 'sp_drv_gettableprivileges', 'anymode'
GO
GRANT EXECUTE ON sp_drv_gettableprivileges TO public
GO
DUMP TRANSACTION master WITH truncate_only
GO
DUMP TRANSACTION sybsystemprocs WITH truncate_only
GO


/*
**  sp_jdbc_getcolumnprivileges 
*/

use sybsystemprocs 
go

if exists (select * from sysobjects where name = 'sp_jdbc_getcolumnprivileges')
	begin
		drop procedure sp_jdbc_getcolumnprivileges
	end
go
/** SECTION END: CLEANUP **/

create procedure sp_jdbc_getcolumnprivileges (
	@table_qualifier varchar(32 ) = null,
	@table_owner varchar(32 ) = null,
	@table_name varchar(300)= null,
	@column_name varchar(255) = null)
AS        

/* Don't delete the following line. It is the checkpoint for sed */
/* Server dependent stored procedure add here ad ADDPOINT_COLPRIVS */
exec sp_drv_getcolumnprivileges @table_name, @table_owner, @table_qualifier, @column_name 

go

exec sp_procxmode 'sp_jdbc_getcolumnprivileges', 'anymode'
go

grant execute on sp_jdbc_getcolumnprivileges to public
go

dump transaction sybsystemprocs with truncate_only 
go

/*
**  End of sp_jdbc_getcolumnprivileges 
*/


/*
**  sp_jdbc_gettableprivileges 
*/

/*
** sp_jdbc_gettableprivileges requires one auxiliary stored procedure/ 
** We do the cleanup and then load that proc first
*/

/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects where name = 'sp_jdbc_gettableprivileges')
	begin
		drop procedure sp_jdbc_gettableprivileges
	end
go

if exists (select * from sysobjects where name = 'sp_jdbc_computeprivs')
	begin
		drop procedure sp_jdbc_computeprivs
	end
go

/** SECTION END: CLEANUP **/

/*
** The results_table, sysprotects, useful_groups, distinct_grantors and
** column_privileges tables need to be created so that sp_jdbc_computeprivs
** has a temp table to reference when the procedure is compiled.
** Otherwise, the calling stored procedure will create the temp tables for
** sp_jdbc_computeprivs. Note that it was necessary to put the table creation
** in the calling stored procedure because in transactions, when you nest
** stored procs, the inner proc cannot create temp tables because they
** get lost when the procedure exits (and thus, a rollback could not be
** executed if one was desired). That caused a server error.
*/
create table #results_table
		(table_qualifier        varchar (32 ),
		 table_owner            varchar (32 ),
		 table_name             varchar (255),
		 column_name            varchar (255) NULL,
		 grantor                varchar (32 ),
		 grantee                varchar (32 ),
		 privilege              varchar (32 ),
		 is_grantable           varchar (3))
go

create table #sysprotects
		(uid            int,
		 action         smallint,
		 protecttype    tinyint,
		 columns        varbinary (133) NULL,
		 grantor        int)
go

create table #useful_groups
		(grp_id         int)
go

create table #distinct_grantors
		(grantor        int)
go

create table #column_privileges
		(grantee_gid    int,
		 grantor        int,
		 grantee        int,
		 insertpriv     tinyint,
		 insert_go      tinyint NULL,
		 deletepriv     tinyint,
		 delete_go      tinyint NULL,
		 selectpriv     varbinary (133) NULL,
		 select_go      varbinary (133) NULL,
		 updatepriv     varbinary (133) NULL,
		 update_go      varbinary (133) NULL,
		 referencepriv  varbinary (133) NULL,
		 reference_go   varbinary (133) NULL)
go


create procedure sp_jdbc_computeprivs (
						@table_name             varchar(255),
						@table_owner            varchar(32 ),
						@table_qualifier        varchar(32 ),
						@column_name            varchar(255),
						@calledfrom_colpriv     tinyint,
						@tab_id                 int)

AS

/* Don't delete the following line. It is the checkpoint for sed */
/* Server dependent stored procedure add here ad ADDPOINT_COMPUTE_PRIVS */
    declare @grantor_name       varchar (32)    /* the ascii name of grantor.
                                                   used for output */
    declare @grantee_name       varchar (32)    /* the ascii name of grantee.
                                                   used for output */
    declare @col_count          smallint        /* number of columns in
                                                   @table_name */
    declare @grantee            int             /* id of the grantee */
    declare @action             smallint        /* action refers to select,
                                                   update...*/
    declare @columns            varbinary (133) /* bit map of column
                                                   privileges */
    declare @protecttype        tinyint         /* grant/revoke or grant with
                                                   grant option */
    declare @grantor            int             /* id of the grantor of the
                                                   privilege */
    declare @grp_id             int             /* the group a user belongs
                                                   to */
    declare @grant_type         tinyint         /* used as a constant */
    declare @revoke_type        tinyint         /* used as a constant */
    declare @select_action      smallint        /* used as a constant */
    declare @update_action      smallint        /* used as a constant */
    declare @reference_action   smallint        /* used as a constant */
    declare @insert_action      smallint        /* used as a constant */
    declare @delete_action      smallint        /* used as a constant */
    declare @public_select      varbinary (133) /* stores select column bit map
                                                   for public */
    declare @public_reference   varbinary (133) /* stores reference column bit
                                                   map for public */
    declare @public_update      varbinary (133) /* stores update column bit map
                                                   for public */
    declare @public_insert      tinyint         /* stores if insert has been
                                                   granted to public */
    declare @public_delete      tinyint         /* store if delete has been
                                                   granted to public */
    declare @grp_select         varbinary (133) /* stores select column bit map
                                                   for group */
    declare @grp_update         varbinary (133) /* stores update column bit map
                                                   for group */
    declare @grp_reference      varbinary (133) /* stores reference column bit
                                                   map for group */
    declare @grp_delete         tinyint         /* if group hs been granted
                                                   delete privilege */
    declare @grp_insert         tinyint         /* if group has been granted
                                                   insert privilege */
    declare @inherit_select     varbinary (133) /* stores select column bit map
                                                   for inherited privs*/
    declare @inherit_update     varbinary (133) /* stores update column bit map
                                                   for inherited privs */
    declare @inherit_reference  varbinary (133) /* stores reference column bit
                                                   map for inherited privs */
    declare @inherit_insert     tinyint         /* inherited insert priv */
    declare @inherit_delete     tinyint         /* inherited delete priv */
    declare @select_go          varbinary (133) /* user column bit map of
                                                   select with grant */
    declare @update_go          varbinary (133) /* user column bit map of
                                                   update with grant */
    declare @reference_go       varbinary (133) /* user column bitmap of
                                                   reference with grant */
    declare @insert_go          tinyint         /* user insert priv with
                                                   grant option */
    declare @delete_go          tinyint         /* user delete priv with grant
                                                   option  */
    declare @prev_grantor       int             /* used to detect if the
                                                   grantor has changed between
                                                   two consecutive tuples */
    declare @col_pos            smallint        /* col_pos of the column we are
                                                   interested in. It is used to
                                                   find the col-bit in the
                                                   bitmap */
    declare @owner_id           int             /* id of the owner of the
                                                   table */
    declare @dbid               smallint        /* dbid for the table */
    declare @grantable          varchar (3)     /* 'YES' or 'NO' if the
                                                   privilege is grantable or
                                                   not */
    declare @is_printable       tinyint         /* 1, if the privilege info is
                                                   to be outputted */

    /* this will make sure that all rows are sent even if
    ** the client "set rowcount" is differect
    */

    set rowcount 0

/* 
** Initialize all constants to be used in this procedure
*/

    select @grant_type = 1

    select @revoke_type = 2
   
    select @select_action = 193

    select @reference_action = 151

    select @update_action = 197

    select @delete_action = 196

    select @insert_action = 195

/*
** compute the table owner id
*/

    select @owner_id = uid
    from   sysobjects
    where  id = @tab_id

/*
** note that the temp tables referred to by this stored proc must be created
** in the calling proc
*/

/*
** this cursor scans the distinct grantor, group_id pairs
*/
    declare grp_cursor cursor for
        select distinct grp_id, grantor 
        from #useful_groups, #distinct_grantors
        order by grantor

/* 
** this cursor scans all the protection tuples that represent
** grant/revokes to users only
*/
    declare user_protect cursor for
        select uid, action, protecttype, columns, grantor
        from   #sysprotects
        where  (uid != 0) and
               ((uid >= @@minuserid and uid < @@mingroupid) or 
               (uid > @@maxgroupid and uid <= @@maxuserid)) 


/*
** this cursor is used to scan #column_privileges table to output results
*/
    declare col_priv_cursor cursor for
          select grantor, grantee, insertpriv, insert_go, deletepriv,
              delete_go, selectpriv, select_go, updatepriv, update_go,
              referencepriv, reference_go
          from #column_privileges



/*
** column count is needed for privilege bit-map manipulation
*/
    select @col_count = count (*) 
    from   syscolumns
    where  id = @tab_id


/* 
** populate the temporary sysprotects table #sysprotects
*/

        insert into #sysprotects 
                select uid, action, protecttype, columns, grantor
                from sysprotects
                where (id = @tab_id)               and
                      ((action = @select_action)   or
                      (action = @update_action)    or
                      (action = @reference_action) or
                      (action = @insert_action)    or
                      (action = @delete_action))

/* 
** insert privilege tuples for the table owner. There is no explicit grants
** of these privileges to the owner. So these tuples are not there in
** sysprotects table
*/

if not exists (select * from #sysprotects where (action = @select_action) and
                (protecttype = @revoke_type) and (uid = @owner_id))
begin
        insert into #sysprotects
             values (@owner_id, @select_action, 0, 0x01, @owner_id)
end

if not exists (select * from #sysprotects where (action = @update_action) and
                (protecttype = @revoke_type) and (uid = @owner_id))
begin
        insert into #sysprotects
             values (@owner_id, @update_action, 0, 0x01, @owner_id)
end

if not exists (select * from #sysprotects where (action = @reference_action) and
                (protecttype = @revoke_type) and (uid = @owner_id))
begin
        insert into #sysprotects
             values (@owner_id, @reference_action, 0, 0x01, @owner_id)
end

if not exists (select * from #sysprotects where (action = @insert_action) and
                (protecttype = @revoke_type) and (uid = @owner_id))
begin
        insert into #sysprotects
             values (@owner_id, @insert_action, 0, NULL, @owner_id)
end

if not exists (select * from #sysprotects where (action = @delete_action) and
                (protecttype = @revoke_type) and (uid = @owner_id))
begin
        insert into #sysprotects
             values (@owner_id, @delete_action, 0, NULL, @owner_id)
end


/* 
** populate the #distinct_grantors table with all grantors that have granted
** the privilege to users or to gid or to public on the table_name
*/

    insert into #distinct_grantors 
          select distinct grantor from #sysprotects

/* 
** Populate the #column_privilegs table as a cartesian product of the table
** #distinct_grantors and all the users, other than groups, in the current
** database
*/


    insert into #column_privileges
          select gid, g.grantor, su.uid, 0, 0, 0, 0, 0x00, 0x00, 0x00, 0x00,
              0x00, 0x00
          from sysusers su, #distinct_grantors g
        where  (su.uid != 0) and
               ((su.uid >= @@minuserid and su.uid < @@mingroupid) or
               (su.uid > @@maxgroupid and su.uid <= @@maxuserid)) 

/*
** populate #useful_groups with only those groups whose members have been
** granted/revoked privileges on the @tab_id in the current database. It also
** contains those groups that have been granted/revoked privileges explicitly
*/

    insert into #useful_groups
        select distinct gid
        from   sysusers su, #sysprotects sp
        where  (su.uid = sp.uid) 


    open grp_cursor

    fetch grp_cursor into @grp_id, @grantor

    /* 
    ** This loop computes all the inherited privilegs of users due
    ** their membership in a group
    */

    while (@@sqlstatus != 2)
   
    begin

         /* 
         ** initialize variables 
         */
         select @public_select = 0x00
         select @public_update = 0x00
         select @public_reference = 0x00
         select @public_delete = 0
         select @public_insert = 0


         /* get the select privileges granted to PUBLIC */

         if (exists (select * from #sysprotects 
                     where (grantor = @grantor) and 
                           (uid = 0) and
                           (action = @select_action)))
         begin
              /* note there can't be any revoke row for PUBLIC */
              select @public_select = columns
              from #sysprotects
              where (grantor = @grantor) and 
                    (uid = 0) and
                    (action = @select_action)
         end


         /* get the update privilege granted to public */
         if (exists (select * from #sysprotects 
                     where (grantor = @grantor) and 
                           (uid = 0) and
                           (action = @update_action)))
         begin
              /* note there can't be any revoke row for PUBLIC */
              select @public_update = columns
              from #sysprotects
              where (grantor = @grantor) and 
                    (uid = 0) and
                    (action = @update_action)
         end

         /* get the reference privileges granted to public */
         if (exists (select * from #sysprotects 
                     where (grantor = @grantor) and 
                           (uid = 0) and
                           (action = @reference_action)))
         begin
              /* note there can't be any revoke row for PUBLIC */
              select @public_reference = columns
              from #sysprotects
              where (grantor = @grantor) and 
                    (uid = 0) and
                    (action = @reference_action)
         end


         /* get the delete privilege granted to public */
         if (exists (select * from #sysprotects 
                     where (grantor = @grantor) and 
                           (uid = 0) and
                           (action = @delete_action)))
         begin
              /* note there can't be any revoke row for PUBLIC */
              select @public_delete = 1
         end

         /* get the insert privileges granted to public */
         if (exists (select * from #sysprotects 
                     where (grantor = @grantor) and 
                           (uid = 0) and
                           (action = @insert_action)))
         begin
              /* note there can't be any revoke row for PUBLIC */
              select @public_insert = 1
         end


         /*
         ** initialize group privileges 
         */

         select @grp_select = 0x00
         select @grp_update = 0x00
         select @grp_reference = 0x00
         select @grp_insert = 0
         select @grp_delete = 0

         /* 
         ** if the group id is other than PUBLIC, we need to find the grants to
         ** the group also 
         */

         if (@grp_id <> 0)
         begin
                /* find select privilege granted to group */
                if (exists (select * from #sysprotects 
                            where (grantor = @grantor) and 
                                  (uid = @grp_id) and
                                  (protecttype = @grant_type) and
                                  (action = @select_action)))
                begin
                        select @grp_select = columns
                        from #sysprotects
                        where (grantor = @grantor) and 
                              (uid = @grp_id) and
                              (protecttype = @grant_type) and 
                              (action = @select_action)
                end

                /* find update privileges granted to group */
                if (exists (select * from #sysprotects 
                            where (grantor = @grantor) and 
                                  (uid = @grp_id) and
                                  (protecttype = @grant_type) and
                                  (action = @update_action)))
                begin
                        select @grp_update = columns
                        from #sysprotects
                        where (grantor = @grantor) and 
                              (uid = @grp_id) and
                              (protecttype = @grant_type) and 
                              (action = @update_action)
                end

                /* find reference privileges granted to group */
                if (exists (select * from #sysprotects 
                            where (grantor = @grantor) and 
                                  (uid = @grp_id) and
                                  (protecttype = @grant_type) and
                                  (action = @reference_action)))
                begin
                        select @grp_reference = columns
                        from #sysprotects
                        where (grantor = @grantor) and 
                              (uid = @grp_id) and
                              (protecttype = @grant_type) and 
                              (action = @reference_action)
                end

                /* find delete privileges granted to group */
                if (exists (select * from #sysprotects 
                            where (grantor = @grantor) and 
                                  (uid = @grp_id) and
                                  (protecttype = @grant_type) and
                                  (action = @delete_action)))
                begin

                        select @grp_delete = 1
                end

                /* find insert privilege granted to group */
                if (exists (select * from #sysprotects 
                            where (grantor = @grantor) and 
                                  (uid = @grp_id) and
                                  (protecttype = @grant_type) and
                                  (action = @insert_action)))
                begin

                        select @grp_insert = 1

                end

         end

         /* at this stage we have computed all the grants to PUBLIC as well as
         ** the group by a specific grantor that we are interested in. Now we
         ** will use this info to compute the overall inherited privileges by
         ** the users due to their membership to the group or to PUBLIC 
         */


         exec sybsystemprocs.dbo.syb_aux_privunion @public_select, @grp_select,
             @col_count, @inherit_select output
         exec sybsystemprocs.dbo.syb_aux_privunion @public_update, @grp_update, 
             @col_count, @inherit_update output
         exec sybsystemprocs.dbo.syb_aux_privunion @public_reference,
             @grp_reference, @col_count, @inherit_reference output

         select @inherit_insert = @public_insert + @grp_insert
         select @inherit_delete = @public_delete + @grp_delete

         /*
         ** initialize group privileges to store revokes
         */

         select @grp_select = 0x00
         select @grp_update = 0x00
         select @grp_reference = 0x00
         select @grp_insert = 0
         select @grp_delete = 0

         /* 
         ** now we need to find if there are any revokes on the group under
         ** consideration. We will subtract all privileges that are revoked
         ** from the group from the inherited privileges
         */

         if (@grp_id <> 0)
         begin
                /* check if there is a revoke row for select privilege*/
                if (exists (select * from #sysprotects 
                            where (grantor = @grantor) and 
                                  (uid = @grp_id) and
                                  (protecttype = @revoke_type) and
                                  (action = @select_action)))
                begin
                        select @grp_select = columns
                        from #sysprotects
                        where (grantor = @grantor) and 
                              (uid = @grp_id) and
                              (protecttype = @revoke_type) and 
                              (action = @select_action)
                end

                /* check if there is a revoke row for update privileges */
                if (exists (select * from #sysprotects 
                            where (grantor = @grantor) and 
                                  (uid = @grp_id) and
                                  (protecttype = @revoke_type) and
                                  (action = @update_action)))
                begin
                        select @grp_update = columns
                        from #sysprotects
                        where (grantor = @grantor) and 
                              (uid = @grp_id) and
                              (protecttype = @revoke_type) and 
                              (action = @update_action)
                end

                /* check if there is a revoke row for reference privilege */
                if (exists (select * from #sysprotects 
                            where (grantor = @grantor) and 
                                  (uid = @grp_id) and
                                  (protecttype = @revoke_type) and
                                  (action = @reference_action)))
                begin
                        select @grp_reference = columns
                        from #sysprotects
                        where (grantor = @grantor) and 
                              (uid = @grp_id) and
                              (protecttype = @revoke_type) and 
                              (action = @reference_action)
                end

                /* check if there is a revoke row for delete privilege */
                if (exists (select * from #sysprotects 
                            where (grantor = @grantor) and 
                                  (uid = @grp_id) and
                                  (protecttype = @revoke_type) and
                                  (action = @delete_action)))
                begin
                        select @grp_delete = 1
                end

                /* check if there is a revoke row for insert privilege */
                if (exists (select * from #sysprotects 
                            where (grantor = @grantor) and 
                                  (uid = @grp_id) and
                                  (protecttype = @revoke_type) and
                                  (action = @insert_action)))
                begin
                        select @grp_insert = 1

                end


                /* 
                ** now subtract the revoked privileges from the group
                */

                exec sybsystemprocs.dbo.syb_aux_privexor @inherit_select,
                                                 @grp_select,
                                                 @col_count,
                                                 @inherit_select output

                exec sybsystemprocs.dbo.syb_aux_privexor @inherit_update,
                                                 @grp_update,
                                                 @col_count,
                                                 @inherit_update output

                exec sybsystemprocs.dbo.syb_aux_privexor @inherit_reference,
                                                 @grp_reference,
                                                 @col_count,
                                                 @inherit_reference output

                if (@grp_delete = 1)
                        select @inherit_delete = 0

                if (@grp_insert = 1)
                        select @inherit_insert = 0

         end

         /*
         ** now update all the tuples in #column_privileges table for this
         ** grantor and group id
         */

         update #column_privileges
         set
                insertpriv      = @inherit_insert,
                deletepriv      = @inherit_delete,
                selectpriv      = @inherit_select,
                updatepriv      = @inherit_update,
                referencepriv   = @inherit_reference

         where (grantor     = @grantor) and
               (grantee_gid = @grp_id)


         /*
         ** the following update updates the privileges for those users
         ** whose groups have not been explicitly granted privileges by the
         ** grantor. So they will all have all the privileges of the PUBLIC
         ** that were granted by the current grantor
         */

         select @prev_grantor = @grantor         
         fetch grp_cursor into @grp_id, @grantor

         if ((@prev_grantor <> @grantor) or (@@sqlstatus = 2))

         begin
         /* Either we are at the end of the fetch or we are switching to
         ** a different grantor. 
         */

               update #column_privileges 
               set
                        insertpriv      = @public_insert,
                        deletepriv      = @public_delete,
                        selectpriv      = @public_select,
                        updatepriv      = @public_update,
                        referencepriv   = @public_reference
                from #column_privileges cp
                where (cp.grantor = @prev_grantor) and
                      (not EXISTS (select * 
                                   from #useful_groups ug
                                   where ug.grp_id = cp.grantee_gid))

         end
    end


    close grp_cursor


    /* 
    ** At this stage, we have populated the #column_privileges table with
    ** all the inherited privileges
    */
    /*
    ** update #column_privileges to give all access to the table owner that way
    ** if there are any revoke rows in sysprotects, then the calculations will
    ** be done correctly.  There will be no revoke rows for table owner if
    ** privileges are revoked from a group that the table owner belongs to.
    */
    update #column_privileges
    set
        insertpriv      = 0x01, 
        deletepriv      = 0x01, 
        selectpriv      = 0x01,
        updatepriv      = 0x01,
        referencepriv   = 0x01

        where grantor = grantee
          and grantor = @owner_id

    
    /* 
    ** Now we will go through each user grant or revoke in table #sysprotects
    ** and update the privileges in #column_privileges table
    */
    open user_protect

    fetch user_protect into @grantee, @action, @protecttype, @columns, @grantor

    while (@@sqlstatus != 2)
    begin
        /*
        ** In this loop, we can find grant row, revoke row or grant with grant
        ** option row. We use protecttype to figure that. If it is grant, then
        ** the user specific privileges are added to the user's inherited
        ** privileges. If it is a revoke, then the revoked privileges are
        ** subtracted from the inherited privileges. If it is a grant with
        ** grant option, we just store it as is because privileges can
        ** only be granted with grant option to individual users
        */

        /* 
        ** for select action
        */
        if (@action = @select_action)
        begin
             /* get the inherited select privilege */
             select @inherit_select = selectpriv
             from   #column_privileges
             where  (grantee = @grantee) and
                    (grantor = @grantor)

             if (@protecttype = @grant_type)
             /* the grantee has a individual grant */
                exec sybsystemprocs.dbo.syb_aux_privunion @inherit_select,
                    @columns, @col_count, @inherit_select output

             else 
                if (@protecttype = @revoke_type)
                /* it is a revoke row */
                     exec sybsystemprocs.dbo.syb_aux_privexor @inherit_select,
                         @columns, @col_count, @inherit_select output

                else
                     /* it is a grant with grant option */

                     select @select_go = @columns

             /* modify the privileges for this user */

             if ((@protecttype = @revoke_type) or (@protecttype = @grant_type))
             begin
                  update #column_privileges
                  set selectpriv = @inherit_select
                  where (grantor = @grantor) and
                        (grantee = @grantee)
             end
             else
             begin

                  update #column_privileges
                  set select_go = @select_go
                  where (grantor = @grantor) and
                        (grantee = @grantee)
             end
        end

        /*
        ** update action
        */
        if (@action = @update_action)
        begin
             /* find out the inherited update privilege */
             select @inherit_update = updatepriv
             from   #column_privileges
             where  (grantee = @grantee) and
                    (grantor = @grantor)


             if (@protecttype = @grant_type)
             /* user has an individual grant */
                exec sybsystemprocs.dbo.syb_aux_privunion @inherit_update,
                    @columns, @col_count, @inherit_update output

             else 
                if (@protecttype = @revoke_type)
                     exec sybsystemprocs.dbo.syb_aux_privexor @inherit_update,
                         @columns, @col_count, @inherit_update output

                else
                     /* it is a grant with grant option */
                     select @update_go = @columns


             /* modify the privileges for this user */

             if ((@protecttype = @revoke_type) or (@protecttype = @grant_type))
             begin
                  update #column_privileges
                  set updatepriv = @inherit_update
                  where (grantor = @grantor) and
                        (grantee = @grantee)
             end
             else
             begin
                  update #column_privileges
                  set update_go = @update_go
                  where (grantor = @grantor) and
                        (grantee = @grantee)
             end
        end

        /* it is the reference privilege */
        if (@action = @reference_action)
        begin
             select @inherit_reference = referencepriv
             from   #column_privileges
             where  (grantee = @grantee) and
                    (grantor = @grantor)


             if (@protecttype = @grant_type)
             /* the grantee has a individual grant */
                exec sybsystemprocs.dbo.syb_aux_privunion @inherit_reference,
                    @columns, @col_count, @inherit_reference output

             else 
                if (@protecttype = @revoke_type)
                /* it is a revoke row */
                     exec sybsystemprocs.dbo.syb_aux_privexor
                        @inherit_reference, @columns, @col_count,
                        @inherit_reference output

                else
                     /* it is a grant with grant option */
                     select @reference_go = @columns


             /* modify the privileges for this user */

             if ((@protecttype = @revoke_type) or (@protecttype = @grant_type))
             begin
                  update #column_privileges
                  set referencepriv = @inherit_reference
                  where (grantor = @grantor) and
                        (grantee = @grantee)
             end
             else
             begin
                  update #column_privileges
                  set reference_go = @reference_go
                  where (grantor = @grantor) and
                        (grantee = @grantee)
             end

        end

        /*
        ** insert action
        */

        if (@action = @insert_action)
        begin
             if (@protecttype = @grant_type)
                   select @inherit_insert = 1
             else
                 if (@protecttype = @revoke_type)
                      select @inherit_insert = 0
                 else
                      select @insert_go = 1

             
             /* modify the privileges for this user */

             if ((@protecttype = @revoke_type) or (@protecttype = @grant_type))
             begin
                  update #column_privileges
                  set insertpriv = @inherit_insert
                  where (grantor = @grantor) and
                        (grantee = @grantee)
             end
             else
             begin
                  update #column_privileges
                  set insert_go = @insert_go
                  where (grantor = @grantor) and
                        (grantee = @grantee)
             end

        end

        /* 
        ** delete action
        */

        if (@action = @delete_action)
        begin
             if (@protecttype = @grant_type)
                   select @inherit_delete = 1
             else
                 if (@protecttype = @revoke_type)
                      select @inherit_delete = 0
                 else
                      select @delete_go = 1

             
             /* modify the privileges for this user */

             if ((@protecttype = @revoke_type) or (@protecttype = @grant_type))
             begin
                  update #column_privileges
                  set deletepriv = @inherit_delete
                  where (grantor = @grantor) and
                        (grantee = @grantee)
             end
             else
             begin
                  update #column_privileges
                  set delete_go = @delete_go
                  where (grantor = @grantor) and
                        (grantee = @grantee)
             end

        end

        fetch user_protect into @grantee, @action, @protecttype, @columns,
            @grantor
    end

    close user_protect

open col_priv_cursor
fetch col_priv_cursor into @grantor, @grantee, @inherit_insert, @insert_go,
                         @inherit_delete, @delete_go, @inherit_select,
                         @select_go, @inherit_update, @update_go,
                         @inherit_reference, @reference_go

while (@@sqlstatus != 2)
begin

      /* 
      ** name of the grantor
      */
      select @grantor_name = name 
      from   sysusers
      where  uid = @grantor


      /*
      ** name of the grantee
      */

      select @grantee_name = name
      from   sysusers
      where  uid = @grantee

      /* 
      ** At this point, we are either printing privilege information for a
      ** a specific column or for table_privileges
      */

            select @col_pos = 0

            if (@calledfrom_colpriv = 1)
            begin
            /* 
            ** find the column position
            */
                 select @col_pos = colid
                 from syscolumns
                 where (id = @tab_id) and
                       (name = @column_name)
            end

            /* 
            ** check for insert privileges
            */
            /* insert privilege is only a table privilege */
            if (@calledfrom_colpriv = 0)
            begin
                    exec sybsystemprocs.dbo.syb_aux_printprivs 
                        @calledfrom_colpriv, @col_pos, @inherit_insert,
                        @insert_go, 0x00, 0x00, 0, @grantable output,
                        @is_printable output

                    if (@is_printable = 1)
                    begin
                          insert into #results_table
                               values (@table_qualifier, @table_owner,
                                       @table_name, @column_name,
                                       @grantor_name, @grantee_name, 'INSERT',
                                       @grantable)
                    end
            end

            /* 
            ** check for delete privileges
            */

            if (@calledfrom_colpriv = 0)
            /* delete privilege need only be printed if called from
               sp_table_privileges */
            begin
                    exec sybsystemprocs.dbo.syb_aux_printprivs 
                         @calledfrom_colpriv, @col_pos, @inherit_delete,
                         @delete_go, 0x00, 0x00, 0, @grantable output,
                         @is_printable output

                    if (@is_printable = 1)
                    begin
                        insert into #results_table
                                values (@table_qualifier, @table_owner,
                                        @table_name, @column_name,
                                        @grantor_name, @grantee_name, 'DELETE',
                                        @grantable)
                    end
            end

            /* 
            ** check for select privileges
            */
            exec sybsystemprocs.dbo.syb_aux_printprivs 
                        @calledfrom_colpriv, @col_pos, 0, 0, @inherit_select,
                        @select_go, 1, @grantable output, @is_printable output


            if (@is_printable = 1)
            begin
                  insert into #results_table
                         values (@table_qualifier, @table_owner, @table_name,
                                 @column_name, @grantor_name, @grantee_name,
                                 'SELECT', @grantable)
            end
            /* 
            ** check for update privileges
            */
            exec sybsystemprocs.dbo.syb_aux_printprivs 
                @calledfrom_colpriv, @col_pos, 0, 0, @inherit_update,
                @update_go, 1, @grantable output, @is_printable output

            if (@is_printable = 1)
            begin
                  insert into #results_table
                        values (@table_qualifier, @table_owner, @table_name,
                                @column_name, @grantor_name, @grantee_name,
                                'UPDATE', @grantable)
            end
            /*
            ** check for reference privs
            */
            exec sybsystemprocs.dbo.syb_aux_printprivs 
                @calledfrom_colpriv, @col_pos, 0, 0, @inherit_reference,
                @reference_go, 1, @grantable output, @is_printable output

            if (@is_printable = 1)
            begin
                insert into #results_table
                        values (@table_qualifier, @table_owner, @table_name,
                                @column_name, @grantor_name, @grantee_name,
                                'REFERENCE', @grantable)
            end



      fetch col_priv_cursor into @grantor, @grantee, @inherit_insert,
          @insert_go, @inherit_delete, @delete_go, @inherit_select, @select_go,
          @inherit_update, @update_go, @inherit_reference, @reference_go
end

close col_priv_cursor


go

/*
** Drop temp tables used for creation of sp_jdbc_computeprivs
*/
drop table #results_table
go
drop table #column_privileges
go
drop table #distinct_grantors
go
drop table #sysprotects
go
drop table #useful_groups
go

exec sp_procxmode 'sp_jdbc_computeprivs', 'anymode'
go

grant execute on sp_jdbc_computeprivs to public
go

create procedure sp_jdbc_gettableprivileges (
	@table_qualifier    varchar(32 ),
	@table_owner        varchar(32 ) = null,
	@table_name         varchar(771)= null)
AS       

/* Sed replace tag point ADDPOINT_TABLE_PRIVS */
exec sp_drv_gettableprivileges @table_name, @table_owner, @table_qualifier

go

exec sp_procxmode 'sp_jdbc_gettableprivileges', 'anymode'
go

grant execute on sp_jdbc_gettableprivileges to public
go

dump transaction sybsystemprocs with truncate_only 
go

/*
**  End of sp_jdbc_gettableprivileges 
*/


/*Stored procedure to support CTS test suite  ADDPOINT_GETCATALOGS_CTS*/
/*
** sp_jdbc_getcatalogs_cts
*/
if exists (select * from sysobjects where name = 'sp_jdbc_getcatalogs_cts')
    begin
        drop procedure sp_jdbc_getcatalogs_cts
    end
go
/** SECTION END: CLEANUP **/


CREATE PROCEDURE sp_jdbc_getcatalogs_cts
as
declare @dbname varchar(32)
declare @startedInTransaction       bit

    if @@trancount = 0
    begin
        set chained off
    end

    /* check if we're in a transaction, before we try any select statements */
    if (@@trancount > 0)
      select @startedInTransaction = 1
    else
      select @startedInTransaction = 0

    set transaction isolation level 1

    if (@startedInTransaction = 1)
       save transaction jdbc_keep_temptables_from_tx

    /* this will make sure that all rows are sent even if
    ** the client "set rowcount" is differect
    */

    set rowcount 0

    create table #tmpcatalog
    ( TABLE_CAT  varchar (32) null)

    DECLARE jcurs_getcatalog CURSOR
    FOR select name from master..sysdatabases FOR READ ONLY
    OPEN  jcurs_getcatalog
    FETCH jcurs_getcatalog INTO  @dbname

    while (@@sqlstatus = 0)
    begin
        insert into #tmpcatalog values(@dbname)
        FETCH jcurs_getcatalog INTO @dbname
    end
    close jcurs_getcatalog
    deallocate cursor jcurs_getcatalog
    select TABLE_CAT  from #tmpcatalog order by TABLE_CAT
    drop table #tmpcatalog

    if (@startedInTransaction = 1)
        rollback transaction jdbc_keep_temptables_from_tx
go

exec sp_procxmode 'sp_jdbc_getcatalogs_cts', 'anymode'
go

grant execute on sp_jdbc_getcatalogs_cts to public
go

/*
**  End of sp_jdbc_getcatalogs_cts
*/

/* 
** sp_jdbc_getcatalogs
*/

/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects where name = 'sp_jdbc_getcatalogs')
	begin
		drop procedure sp_jdbc_getcatalogs
	end
go
/** SECTION END: CLEANUP **/


CREATE PROCEDURE sp_jdbc_getcatalogs 
as

	if @@trancount = 0
	begin
		set chained off
	end

	set transaction isolation level 1
	/* this will make sure that all rows are sent even if
	** the client "set rowcount" is differect
	*/

	set rowcount 0

	select TABLE_CAT=name from master..sysdatabases order by name 
go

exec sp_procxmode 'sp_jdbc_getcatalogs', 'anymode'
go

grant execute on sp_jdbc_getcatalogs to public
go

commit
go
dump transaction sybsystemprocs with truncate_only 
go

/* 
**  End of sp_jdbc_getcatalogs
*/


/*
**  sp_jdbc_primarykey
*/

/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select *
	from sysobjects
		where sysstat & 7 = 4
			and name = 'sp_jdbc_primarykey')
begin
	drop procedure sp_jdbc_primarykey
end
go
/** SECTION END: CLEANUP **/

/*
** Altered from the ODBC sp_pkeys defined in sycsp11.sql.
**
** To facilitate eventually combining scripts for ODBC and JDBC,
** only the ordering of the arguments and the final select have been modified.
*/
/*
** note: there is one raiserror message: 18040
**
** messages for 'sp_jdbc_primarykey'               18039, 18040
**
** 17461, 'Object does not exist in this database.'
** 18039, 'table qualifier must be name of current database.'
** 18040, 'catalog procedure %1! can not be run in a transaction.', sp_jdbc_primarykey
**
*/

create procedure sp_jdbc_primarykey
			   @table_qualifier varchar(32 ),
			   @table_owner 	varchar(32 ),
			   @table_name		varchar(300)
as
	declare @msg varchar(255)
	declare @keycnt smallint
	declare @indexid smallint
	declare @indexname varchar(255)
	declare @i int
	declare @id int
	declare @uid smallint
	declare @actual_table_name varchar(300)
	declare @startedInTransaction bit

	if (@@trancount = 0)
	begin
		set chained off
	end

	/* see if we're in a transaction, before we try any select statements */
	if (@@trancount > 0)
		select @startedInTransaction = 1
	else 
		select @startedInTransaction = 0

	/* this will make sure that all rows are sent even if
	** the client "set rowcount" is differect
	*/

	set rowcount 0

	select @actual_table_name = @table_name

	select @id = NULL

	set nocount on

	set transaction isolation level 1

		if (@startedInTransaction = 1)
			save transaction jdbc_keep_temptables_from_tx 

	if @table_qualifier is not null
	begin
		if db_name() != @table_qualifier
		begin	
			/* if qualifier doesn't match current database */
			/* 'table qualifier must be name of current database'*/
			exec sp_getmessage 18039, @msg output
			raiserror 18039 @msg 
			return (2)
		end
	end

	exec sp_jdbc_escapeliteralforlike @table_name

	if @table_owner is null
	begin
		select @table_owner = '%'
	end

	if (select count(*) from sysobjects
		where user_name(uid) like @table_owner ESCAPE '\'
		and ('"' + name + '"' = @table_name or name = @table_name)) = 0
	begin	
		/* 17461, 'Object does not exist in this database.' */
		exec sp_getmessage 17674, @msg output
		raiserror 17674 @msg 
		return (3)
	end

	create table #pkeys(
			 TABLE_CAT       varchar(32 ),
			 TABLE_SCHEM     varchar(32 ),
			 TABLE_NAME      varchar(255),
			 COLUMN_NAME     varchar(255),
			 KEY_SEQ         smallint,
						 PK_NAME         varchar(255))


	DECLARE jcurs_sysuserobjects CURSOR
		FOR
		select id, uid 
		from sysobjects
		where user_name(uid) like @table_owner ESCAPE '\'
		and name = @table_name
		FOR READ ONLY

	OPEN  jcurs_sysuserobjects

	FETCH jcurs_sysuserobjects INTO @id, @uid 

	while (@@sqlstatus = 0)
	begin

		/*
		**  now we search for primary key (only declarative) constraints
		**  There is only one primary key per table.
		*/

		select @keycnt = keycnt, @indexid = indid, @indexname = name
		from   sysindexes
		where  id = @id
		and indid > 0 /* make sure it is an index */
		and status2 & 2 = 2 /* make sure it is a declarative constr */
		and status & 2048 = 2048 /* make sure it is a primary key */

		/*
		** For non-clustered indexes, keycnt as returned from sysindexes is one
		** greater than the actual key count. So we need to reduce it by one to
		** get the actual number of keys.
		*/
		if (@indexid >= 2)
		begin
			select @keycnt = @keycnt - 1
		end

		select @i = 1

		while @i <= @keycnt
		begin
			insert into #pkeys values
			(db_name(), user_name(@uid), @actual_table_name,
				index_col(@actual_table_name, @indexid, @i, @uid), @i, @indexname)
			select @i = @i + 1
		end

		/*
		** Go to the next user/object
		*/
		FETCH jcurs_sysuserobjects INTO @id, @uid 
	end

	close jcurs_sysuserobjects
	deallocate cursor jcurs_sysuserobjects

	/*
	** Original ODBC query:
	**
	** select table_qualifier, table_owner, table_name, column_name, key_seq
	** from #pkeys
	** order by table_qualifier, table_owner, table_name, key_seq
	*/
	/*
	** Primary keys are not explicitly named, so name is always null.
	*/
	select  TABLE_CAT,
			TABLE_SCHEM,
			TABLE_NAME,
			COLUMN_NAME,
			KEY_SEQ,
			PK_NAME
	from #pkeys
	order by COLUMN_NAME

	drop table #pkeys

	if (@startedInTransaction = 1)
		rollback transaction jdbc_keep_temptables_from_tx 

	return (0)
go
exec sp_procxmode 'sp_jdbc_primarykey', 'anymode'
go
grant execute on sp_jdbc_primarykey to public
go
use sybsystemprocs 
go

/*
**  End of sp_jdbc_primarykey
*/



/*
**  sp_sql_type_name
*/

/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects where name = 'sp_sql_type_name')
begin
		drop procedure sp_sql_type_name
end
go
/** SECTION END: CLEANUP **/

/*
**  Implements RSMDA.getColumnTypeName
**  create a procedure that will query 
**  spt_jdbc_datatype_info for the correct jdbc mapped datatype or
**  the datasource specific systable, to retrieve the correct type
**  or user defined datatype name, based on the parameters
**  @datatype = the protocol datatype value
**  @usrtype = the data source specifc user defined datatype value
*/
create procedure sp_sql_type_name
		@datatype  tinyint,
		@usrtype   smallint
as
BEGIN

	if @@trancount = 0
	begin
		set chained off
	end

	set transaction isolation level 1

	/* this will make sure that all rows are sent even if
	** the client "set rowcount" is differect
	*/

	set rowcount 0

/* Special case for types numericn, decimaln, daten and timen.
** They do not seem to have the correct mapping of usertype & datatype
*/
   /* if type is decimaln(106) map to decimal(55)
	* if type is numericn(108) map to numeric(63) 
	* if type is daten (123) map to date (49)
	* if type is timen (147) map to time (51)
	* if type is bigdatetimen (187) map to bigdatetime (189)
	* if type is bigtimen (188) map to bigtime (190)
	*/
   if (@datatype = 108)
   begin
	select @datatype = 63
   end
   else if (@datatype = 106)
   begin
	select @datatype = 55
   end
   else if (@datatype = 123)
   begin
	select @datatype = 49
   end
   else if (@datatype = 147)
   begin
	select @datatype = 51
   end
   else if (@datatype = 187)
   begin
		select @datatype = 189
   end
   else if (@datatype = 188)
   begin
		select @datatype = 190
   end


   /* if a usertype is greater than 100 that means it is a
	* user defined datatype, and it needs to be reference in
	* the datasource specific systype table.  If they are
	* user-defined numeric/decimal, then only use the usertype
	* for the search criteria (see the note on SPECIAL CASE below)
	* This is the fix for Bug 192969.
	*/
   if (@usrtype > 100) 
   begin
	 select name from systypes
		where usertype = @usrtype
   end
   /* check if we have the special case of a usertype signaling
	* UNICHAR (34) or UNIVARCHAR (35)
	*/
   else if (@usrtype = 34 or @usrtype = 35)
   begin
	   select name from systypes
		 where usertype = @usrtype
   end
   /* simply check spt_jdbc_datatype_info for 
	* the predefined jdbc mapping for the types
	*/
   else
   begin
	   select j.type_name as name 
	   from sybsystemprocs.dbo.spt_jdbc_datatype_info j
	   where j.ss_dtype = @datatype
   end
END
go
/* end of sp_sql_type_name */
exec sp_procxmode 'sp_sql_type_name', 'anymode'
go
grant execute on sp_sql_type_name to public
go
commit
go
dump transaction sybsystemprocs with truncate_only 
go


/*
**  End of sp_sql_type_name
*/



/*
**  sp_jdbc_getbestrowidentifier
*/

/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select *
	from sysobjects where name = 'sp_jdbc_getbestrowidentifier')
begin
	drop procedure sp_jdbc_getbestrowidentifier
end
go
/** SECTION END: CLEANUP **/


/* Get a description of a table's optimal set of columns that uniquely 
** identifies a row
** Usually it's the unique primary key index column or the identity field
*/

create procedure sp_jdbc_getbestrowidentifier (
				 @table_qualifier	varchar(32 ) = null,
				 @table_owner		varchar(32 ) = null,
				 @table_name		varchar(255),
				 @scope			int,
				 @nullable		smallint)
as
	declare @indid              int
	declare @table_id           int
	declare @dbname             varchar(32 )
	declare @owner              varchar(32 )
	declare @full_table_name    varchar(765)
	declare @msg                varchar(765)

	if @@trancount = 0
	begin
		set chained off
	end

	set transaction isolation level 1

		/* this will make sure that all rows are sent even if
		** the client "set rowcount" is differect
		*/

		set rowcount 0


		if exists (select * from sysobjects where name = '#bestinfo')
		begin
		drop table #bestinfo
		end
		create table #bestinfo (
			SCOPE smallint, COLUMN_NAME varchar(255),
			DATA_TYPE smallint, TYPE_NAME varchar(255),
			COLUMN_SIZE int, BUFFER_LENGTH varchar(255),
			DECIMAL_DIGITS smallint, PSEUDO_COLUMN smallint)

	/* get database name */
	select @dbname = db_name()

	/* we don't want a temp table unless we're in tempdb */
	   /* Adding tempdb check here depending on the ASE version ADDTEMPDB */

   if (@table_name like '#%' and db_name() != db_name(tempdb_id()))
	begin	
		exec sp_getmessage 17676, @msg output
		raiserror 17676 @msg
		return (1)
	end

	if @table_qualifier is not null
	begin
		/* if qualifier doesn't match current database */
		if @dbname != @table_qualifier
		begin	
			exec sp_getmessage 18039, @msg output
			raiserror 18039 @msg
			return (1)
		end
	end

	if (@table_owner is null) 
	begin
		select @table_owner ='%'
	end
	else
	begin

		if (charindex('%',@table_owner) > 0)
		begin
			exec sp_getmessage 17993, @msg output
			raiserror 17993 @msg, @table_owner
			return(1)
		end

		/*
		** if there is a '_' character in @table_owner, 
		** then we need to make it work literally in the like
		** clause.
		*/
		if (charindex('_', @table_owner) > 0)
		begin
			exec sp_jdbc_escapeliteralforlike
				@table_owner output
		end
	end


	if (@table_name is null) 
	begin
	   exec sp_getmessage 17993, @msg output
	   raiserror 17993 @msg, 'NULL'
	   return(1)
	end

	if ((select count(*) 
		from sysobjects
		where user_name(uid) like @table_owner ESCAPE '\'
		and name = @table_name) = 0)
	begin
	  exec sp_getmessage 17674, @msg output
	  raiserror 17674 @msg, @table_name
	  return
	end

	declare owner_cur cursor for 
		select @table_owner = user_name(uid) from sysobjects 
			where name like @table_name ESCAPE '\' 
				and user_name(uid) like @table_owner ESCAPE '\' 
	open owner_cur
	fetch owner_cur into @owner
	while (@@sqlstatus = 0)
	begin
		select @full_table_name = @owner + '.' + @table_name

		/* get object ID */
		select @table_id = object_id(@full_table_name)

		/* ROWID, now find the id of the 'best' index for this table */

		select @indid = (
			select min(indid)
			from sysindexes
			where
				id = @table_id
				and indid > 0)		/* eliminate table row */

		/* Sybase's only PSEUDO_COLUMN is called SYB_IDENTITY_COL and */
		/* is only generated when dboption 'auto identity' is set on */
		if exists (select name from syscolumns where id=@table_id and name =
			'SYB_IDENTITY_COL')
		begin
			insert into #bestinfo values (
				convert(smallint, 0), 'SYB_IDENTITY_COL', 2, 'NUMERIC', 10,
				'not used', 0, 2)
		end
		else
		begin
			insert into #bestinfo 
		select
				convert(smallint, 0),index_col(@full_table_name,indid,c.colid),
				d.data_type + convert(smallint, isnull(d.aux,
						ascii(substring('666AAA@@@CB??GG',
						2*(d.ss_dtype%35+1)+2-8/c2.length,1))
						-60)),
				rtrim(substring(d.type_name, 1 + isnull(d.aux,
						ascii(substring('III<<<MMMI<<A<A',
						2*(d.ss_dtype%35+1)+2-8/c2.length, 1))
						-60), 18)),
				isnull(d.data_precision, convert(int,c2.length))
						+ isnull(d.aux, convert(int,
						ascii(substring('???AAAFFFCKFOLS',
						2*(d.ss_dtype%35+1)+2-8/c2.length,1))
						-60)),
				'not used',
					/*isnull(d.length, convert(int,c2.length))
						+ convert(int, isnull(d.aux,
						ascii(substring('AAA<BB<DDDHJSPP',
						2*(d.ss_dtype%35+1)+2-8/c2.length, 1))
						-64)),*/
				isnull(d.numeric_scale, convert(smallint,
						isnull(d.aux,
						ascii(substring('<<<<<<<<<<<<<<?',
						2*(d.ss_dtype%35+1)+2-8/c2.length, 1))
						-60))),
				1
		from
			sysindexes x,
			syscolumns c,
			sybsystemprocs.dbo.spt_jdbc_datatype_info d,
			systypes t,
			syscolumns c2	/* self-join to generate list of index
					** columns and to extract datatype names */
			where
			x.id = @table_id
			and c2.name = index_col(@full_table_name, @indid,c.colid)
			and c2.id =x.id
			and c.id = x.id
			and c.colid < keycnt + (x.status & 16) / 16
			and x.indid = @indid
			and c2.type = d.ss_dtype
			and c2.usertype *= t.usertype
		end

		fetch owner_cur into @owner
	end
	select * from #bestinfo
	drop table #bestinfo
	return (0)
go
exec sp_procxmode 'sp_jdbc_getbestrowidentifier', 'anymode'
go
grant execute on sp_jdbc_getbestrowidentifier to public
go
commit
go
dump transaction sybsystemprocs with truncate_only 
go

/*
**  End of sp_jdbc_getbestrowidentifier
*/

/**
 * sp_jdbc_getisolationlevels
 */

/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs
go

if exists (select * from sysobjects where name = 'sp_jdbc_getisolationlevels')
begin
	drop procedure sp_jdbc_getisolationlevels
end
go
/** SECTION END: CLEANUP **/

/**
 * <P> This procedure is used to determine which transaction isolation
 * levels are supported by this ASE server.  This proc is registered
 * with the spt_mda table to be executed when the user calls:
 * <CODE> DatabaseMetaData.supportsTransactionIsolationLevel(int) </CODE>.
 * If the int specified is found in the row returned by this procedure,
 * then that level is supported.  The levels are indicated by using the
 * integer mappings found in the java.sql.Connection interface.
 * <UL> All ASE versions currently support these levels:
 *              <LI> TRANSACTION_SERIALIZABLE  (8) and
 *              <LI> TRANSACTION_READ_COMMITTED  (2)            </UL>
 * <UL> ASE versions after 10.1 added support for these levels:
 *              <LI>  TRANSACTION_READ_UNCOMMITTED (1).         </UL>
 * <P> This procedure accesses the @@version string, determines the
 * version of ASE, and returns the appropriate levels.
 * <P> WARNING:  Should future versions of ASE support more transaction
 * isolation levels (e.g., TRANSACTION_REPEATABLE_READ (4)), this proc
 * must be modified.
 */

create procedure sp_jdbc_getisolationlevels as

	declare
	  @startVersion    int,         /* index of version # in @@version    */
	  @versionNum      varchar(20), /* whole version number (eg 11.5.1)   */
	  @versionFloat    float,       /* major & minor ver # (eg 11.5)      */
	  @minorVersion    varchar(15), /* minor version (eg 5.1)             */
	  @earliestVersion float,       /* this ver supports READ_UNCOMMITTED */
	  @firstDecimal    int,         /* index of 1st decimal point         */
	  @secondDecimal   int,         /* index of 2nd decimal point         */
	  @endVersion      int	    /* index of end of version number     */

	/* server must be at least 10.1.x to support levels 8, 2, AND 1. */
	select @earliestVersion = 10.1  

	/* find where the version number is in this mess of characters */
	select @startVersion = patindex('%/[0-9]%.%[0-9]/%', @@version) +1

	/* could not find version number in expected format within @@version */
	if (@startVersion <= 1)
	begin
	select 0 /* returning TRANSACTION_NONE */
	return 2
	end

	select @versionNum = substring(@@version, @startVersion, 20)

	/* Find the first decimal point in this version number */
	select @firstDecimal = charindex('.', @versionNum)

	/* extract the minor version and any "sub-minor" version (eg 5.1) */
	select @minorVersion = substring(@versionNum, @firstDecimal + 1, 15)

	/* Find the second decimal point in this version number */
	/* if none found, then "pretend" to have one at the end */
	/* of the version number (where the "/" is)             */

	select @secondDecimal = charindex('.',@minorVersion)
	select @endVersion = charindex('/',@minorVersion)
	if ((@secondDecimal = 0) or (@endVersion < @secondDecimal))
	   select @secondDecimal = @endVersion

	/* Compute major and minor versions as a float (eg "11.5" --> 11.5F) */
	select @versionFloat = convert(float, substring(@versionNum, 1,
										   @secondDecimal+@firstDecimal - 1))

	if (@versionFloat >= @earliestVersion)
	   select 8,2,1
	else
	   select 8,2

	return (0)   
go

exec sp_procxmode 'sp_jdbc_getisolationlevels', 'anymode'
go

grant execute on sp_jdbc_getisolationlevels to public
go

commit
go


/*
**  sp_jdbc_getindexinfo
*/

/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select *
	from sysobjects
		where name = 'sp_jdbc_getindexinfo')
begin
	drop procedure sp_jdbc_getindexinfo
end
go
/** SECTION END: CLEANUP **/


/* getindexinfo returns information on the indexes of a page
** is unique is set to TRUE only indexes on indexes where it's value's must
** be unique are returned.
** garantee alwys accurate data
*/
create procedure sp_jdbc_getindexinfo (
	@table_qualifier	varchar(32 ) = NULL,
	@table_owner		varchar(32 ) = NULL,
	@table_name		varchar(255),
	@unique			varchar(5) ,
	@approximate 		char(5))
as
	declare @indid              int
	declare @lastindid          int
	declare @full_table_name    varchar(765)
	declare @msg                varchar(250)
	declare @tableid            int
	declare @startedInTransaction bit

	if (@@trancount = 0)
	begin
		set chained off
	end 

	/* see if we are already in a transaction */
	if (@@trancount > 0)
		select @startedInTransaction = 1
	else
		select @startedInTransaction = 0

	/* this will make sure that all rows are sent even if
	** the client "set rowcount" is differect
	*/

	set rowcount 0

	/*
	** Verify table qualifier is name of current database.
	*/
	if @table_qualifier is not null
	begin
		if db_name() != @table_qualifier
		begin	/* If qualifier doesn't match current database */
			/*
			** 18039, 'Table qualifier must be name of current database.'
			*/
			exec sp_getmessage 18039, @msg output
			raiserror 18039 @msg
			return (1)
		end
	end
	select @table_qualifier = db_name()

	set transaction isolation level 1

	if (@startedInTransaction = 1)
		save transaction jdbc_keep_temptables_from_tx 

	if (@table_owner is null)
	begin
		select @table_owner ='%'
	end

	if (@table_name is null) 
	begin
	   exec sp_getmessage 17993, @msg output
	   raiserror 17993 @msg, 'NULL'
	   return(1)
	end

	if ((select count(*) 
		from sysobjects 
		where user_name(uid) like @table_owner ESCAPE '\'
		and name = @table_name) = 0)
	begin
		exec sp_getmessage 17674, @msg output
		raiserror 17674 @msg
		return
	end

	create table #TmpIndex(
		TABLE_CAT       varchar(32 ),
		TABLE_SCHEM     varchar(32 ),
		TABLE_NAME      varchar(255),
		INDEX_QUALIFIER varchar(32 ) null,
		INDEX_NAME      varchar(255) null,
		NON_UNIQUE      varchar(5),
		TYPE            smallint,
		ORDINAL_POSITION smallint null,
		COLUMN_NAME     varchar(255) null,
		ASC_OR_DESC     char(1) null,
		index_id	int null,
		CARDINALITY     int null,
		PAGES           int null,
		FILTER_CONDITION varchar(32 ) null,
		status		smallint,
		table_id    int)


	DECLARE jcurs_sysuserobjects CURSOR
		FOR
		select id
		from sysobjects
		where user_name(uid) like @table_owner ESCAPE '\'
		and name = @table_name
		FOR READ ONLY

	OPEN  jcurs_sysuserobjects

	FETCH jcurs_sysuserobjects INTO @tableid 

	while (@@sqlstatus = 0)
	begin
		/*
		** build the full_table_name for use below in 
		** obtaining the index column via the INDEX_COL()
		** internal function
		*/
		select @full_table_name = user_name(uid) + '.' + name
		from sysobjects
		where id = @tableid

		/*
		** Start at lowest index id, while loop through indexes. 
		** Create a row in #TmpIndex for every column in sysindexes, each is
		** followed by an row in #TmpIndex with table statistics for the preceding
		** index.
		*/
		select @indid = min(indid)
			from sysindexes
			where id = @tableid
			and indid > 0
			and indid < 255

		while @indid is not NULL
		begin
			insert #TmpIndex	/* Add all columns that are in index */
			select
				db_name(),		/* table_qualifier */
				user_name(o.uid),	/* table_owner	   */
				o.name,			/* table_name	   */
				o.name, 		/* index_qualifier */
				x.name,			/* index_name	   */
				'FALSE',		/* non_unique	   */
				1,			/* SQL_INDEX_CLUSTERED */
				colid,			/* seq_in_index	   */
				INDEX_COL(@full_table_name,indid,colid),/* column_name	   */
				'A',			/* collation	   */
				@indid,			/* index_id 	   */
/* Server dependent stored procedure add here ADDPOINT_ROWCOUNT*/
                        row_count(db_id(), x.id), /* cardinality        */
                        data_pages(db_id(), x.id,
                                    case
                                        when x.indid = 1
                                            then 0
                                    else x.indid
                                    end),   /* pages           */

				null,			/* Filter condition not available */
							/* in SQL Server*/
				x.status, 		/* Status */	
				@tableid    /* table id, internal use for updating the non_unique field */
			from sysindexes x, syscolumns c, sysobjects o
			where x.id = @tableid
				and x.id = o.id
				and x.id = c.id
				and c.colid < keycnt+(x.status&16)/16
				and x.indid = @indid

			/*
			** only update the inserts for the current
			** owner.table
			*/
			update #TmpIndex
				set NON_UNIQUE = 'TRUE'
				where status&2 != 2 /* If non-unique index */
				and table_id = @tableid

			/*
			** Save last index and increase index id to next higher value.
			*/
			select @lastindid = @indid
			select @indid = NULL

			select @indid = min(indid)
			from sysindexes
			where id = @tableid
				and indid > @lastindid
				and indid < 255
		end

		/* 
		** Now add row with table statistics 
		*/
		insert #TmpIndex
			select
				db_name(),		/* table_qualifier */
				user_name(o.uid),	/* table_owner	   */
				o.name, 		/* table_name	   */
				null,			/* index_qualifier */
				null,			/* index_name	   */
				'FALSE',		/* non_unique	   */
				0,			/* SQL_table_STAT  */
				null,			/* seq_in_index	*/
				null,			/* column_name	   */
				null,			/* collation	   */
				0,			/* index_id 	   */
/* Server dependent stored procedure add here ADDPOINT_ROWCOUNT*/
                        row_count(db_id(), x.id), /* cardinality        */
                        data_pages(db_id(), x.id,
                                    case
                                        when x.indid = 1
                                            then 0
                                    else x.indid
                                    end),   /* pages           */

				null,			/* Filter condition not available */
							/* in SQL Server*/
				0,			/* Status */
				@tableid    /* tableid */
			from sysindexes x, sysobjects o
			where o.id = @tableid
				and x.id = o.id
				and (x.indid = 0 or x.indid = 1)	
			/*  
			** If there are no indexes
			** then table stats are in a row with indid = 0
			*/

		/*
		** Go to the next user/object
		*/
		FETCH jcurs_sysuserobjects INTO @tableid 
	end

	close jcurs_sysuserobjects
	deallocate cursor jcurs_sysuserobjects

	update #TmpIndex
		set
			TYPE = 3,		/* SQL_INDEX_OTHER */
			CARDINALITY = NULL,
			PAGES = NULL
		where index_id > 1		/* If non-clustered index */

	if (@unique!='1')
	begin
		/* If all indexes desired */
		select
			TABLE_CAT,
			TABLE_SCHEM,
			TABLE_NAME,
			NON_UNIQUE,
			INDEX_QUALIFIER,
			INDEX_NAME,
			TYPE,
			ORDINAL_POSITION,
			COLUMN_NAME,
			ASC_OR_DESC,
			CARDINALITY,
			PAGES,
			FILTER_CONDITION			
		from #TmpIndex
		order by NON_UNIQUE, TYPE, INDEX_NAME, ORDINAL_POSITION
	end
	else	
	begin
		/* else only unique indexes desired */
		select
			TABLE_CAT,
			TABLE_SCHEM,
			TABLE_NAME,
			NON_UNIQUE,
			INDEX_QUALIFIER,
			INDEX_NAME,
			TYPE,
			ORDINAL_POSITION,
			COLUMN_NAME,
			ASC_OR_DESC,
			CARDINALITY,
			PAGES,
			FILTER_CONDITION
		from #TmpIndex
		where NON_UNIQUE = 'FALSE' 	
		order by NON_UNIQUE, TYPE, INDEX_NAME, ORDINAL_POSITION

	end

	drop table #TmpIndex

	if (@startedInTransaction = 1)
		rollback transaction jdbc_keep_temptables_from_tx 

	return (0)

go

exec sp_procxmode 'sp_jdbc_getindexinfo', 'anymode'
go
grant execute on sp_jdbc_getindexinfo to public
go
commit
go
dump transaction sybsystemprocs with truncate_only 
go

/*
**  End of sp_jdbc_getindexinfo
*/


/*
**  sp_jdbc_stored_procedures
*/


/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects
		where name = 'sp_jdbc_stored_procedures')
begin
	drop procedure sp_jdbc_stored_procedures
end
go
/** SECTION END: CLEANUP **/


/*
** Altered from the ODBC sp_jdbc_procedures defined in sycsp11.sql.
**
** New column 'PROCEDURE_TYPE' was added to support JDBC spec. This
** column is to indicate if the procedure returns a result. If 0,
** column will be evalued as DatabaseMetadata.procedureResultUnknown;
** this means that the procedure MAY return a result.
*/
/*
** Messages for 'sp_jdbc_stored_procedures'	18041
**
** 18041, 'Stored Procedure qualifier must be name of current database.'
**
*/
create procedure sp_jdbc_stored_procedures
@sp_qualifier	varchar(32 ) = null,	/* stored procedure qualifier; 
					** For the SQL Server, the only valid
					** values are NULL or the current 
					** database name
					*/
@sp_owner   varchar(771) = null,	/* stored procedure owner */
@sp_name    varchar(771) = null,	/* stored procedure name */
@version    int                 = null,  /* Conform to JDBC 4.0 spec if @version is not null*/
@functions  int                 = 0     /* Call from getFunctions()? */
as

declare	@msg	varchar(90)
declare @uid   int
declare @protecttype tinyint
declare @id  int
declare @action    smallint
declare @number smallint
declare @sequence int


if @@trancount = 0
begin
	set chained off
end

set transaction isolation level 1

/* this will make sure that all rows are sent even if
** the client "set rowcount" is differect
*/

set rowcount 0



/* If qualifier is specified */
if @sp_qualifier is not null
begin
	/* If qualifier doesn't match current database */
	if db_name() != @sp_qualifier
	begin
		/* If qualifier is not specified */
		if @sp_qualifier = ''
		begin
			/* in this case, we need to return an empty 
			** result set because the user has requested a 
			** database with an empty name 
			*/
			select @sp_name = ''
			select @sp_owner = ''
		end

		/* qualifier is specified and does not match current database */
		else
		begin	
			/* 
			** 18041, 'Stored Procedure qualifer must be name of
			** current database'
			*/
			exec sp_getmessage 18041, @msg out
			raiserror 18041 @msg
			return (1)
		end
	end
end

/* If procedure name not supplied, match all */
if @sp_name is null
begin  
	select @sp_name = '%'
end

/* If procedure owner not supplied, match all */
if @sp_owner is null	
	select @sp_owner = '%'

/* 
** Retrieve the stored procedures and associated info on them
*/
/*

** get rows for public, current users, user's groups
*/

if @functions = 0
begin 
if (@version is not null)
begin
	select  PROCEDURE_CAT = db_name(),
		PROCEDURE_SCHEM = user_name(o.uid),
		PROCEDURE_NAME = o.name +';'+ ltrim(str(p.number,5)),
		num_input_params = -1,      /* Constant since value unknown */
		num_output_params = -1,         /* Constant since value unknown */
		num_result_sets = -1,       /* Constant since value unknown */
		REMARKS = convert(varchar(254),null),   /* Remarks are NULL */
		PROCEDURE_TYPE = 0,
		SPECIFIC_NAME = o.name +';'+ ltrim(str(p.number,5))
	from sysobjects o, sysprocedures p,sysusers u
	where o.name like @sp_name ESCAPE '\'
		and p.sequence = 0
		and user_name(o.uid) like @sp_owner ESCAPE '\'
		and o.type = 'P'        /* Object type of Procedure */
		and p.id = o.id
		and u.uid = user_id()   /* constrain sysusers uid for use in 
								** subquery 
								*/
		and (suser_id() = 
				(select uid from sysusers where suid = suser_id()) /* User is the System Administrator */
			 or  o.uid = user_id()  /* User created the object */
						/* here's the magic..select the highest 
						** precedence of permissions in the 
						** order (user,group,public)  
						*/

			/*
			** The value of protecttype is
			**
			**      0  for grant with grant
			**      1  for grant and,
			**      2  for revoke
			**
			** As protecttype is of type tinyint, protecttype/2 is
			** integer division and will yield 0 for both types of
			** grants and will yield 1 for revoke, i.e., when
			** the value of protecttype is 2.  The XOR (^) operation
			** will reverse the bits and thus (protecttype/2)^1 will
			** yield a value of 1 for grants and will yield a
			** value of zero for revoke.
			**
			** Normal uids have values upto 16383, roles have uids
			** from 16384 upto 16389 and uids of groups start from
			** 16390 onwards.
			**
			** If there are several entries in the sysprotects table
			** with the same Object ID, then the following expression
			** will prefer an individual uid entry over a group entry
			** and prefer a group entry over a role entry.
			**
				** For example, let us say there are two users u1 and u2
			** with uids 4 and 5 respectiveley and both u1 and u2
			** belong to a group g12 whose uid is 16390.  procedure p1
			** is owned by user u0 and user u0 performs the following
			** actions:
			**
			**      grant exec on p1 to g12
			**      revoke grant on p1 from u1
			**
			** There will be two entries in sysprotects for the object
			** p1, one for the group g12 where protecttype = grant (1)
			** and one for u1 where protecttype = revoke (2).
			**
			** For the group g12, the following expression will
			** evaluate to:
			**
			**      (((+)*abs(16390-16383))*2) + ((1/2)^1))
			**      = ((14) + (0)^1) = 14 + 1 = 15
			**
			** For the user entry u1, it will evaluate to:
			**
			**      (((+)*abs(4-16383)*2) + ((2/2)^1))
			**      = ((abs(-16379)*2 + (1)^1)
			**      = 16379*2 + 0 = 32758
			**
			** As the expression evaluates to a bigger number for the
			** user entry u1, select max() will chose 32758 which,
			** ANDed with 1 gives 0, i.e., sp_jdbc_stored_procedures will
			** not display this particular procedure to the user.
			**
			** When the user u2 invokes sp_jdbc_stored_procedures, there is
			** only one entry for u2, which is the entry for the group
			** g12, and so this entry will be selected thus allowing
			** the procedure in question to be displayed.
			**
			** Notice that multiplying by 2 makes the number an
			** even number (meaning the last digit is 0) so what
			** matters at the end is (protecttype/2)^1.
			**
			*/

			or ((select distinct max(((sign(p.uid)*abs(p.uid-16383))*2)
					 + ((p.protecttype/2)^1))
			   from sysprotects p, sysusers u
					where action in (193,224)
					and u.uid = user_id()
					and (p.uid = 0         /* get rows for public */
					or p.uid = user_id()    /* current user */
					or p.uid = u.gid) 
					and p.id = o.id /* outer join to correlate 
								** with all rows in sysobjects 
								*/
			   )&1              /* more magic...normalize GRANT */
				  ) = 1)        /* final magic...compare Grants */
	order by PROCEDURE_CAT, PROCEDURE_SCHEM, PROCEDURE_NAME, SPECIFIC_NAME
end /* End of if @version is not null */
else
begin
	select  PROCEDURE_CAT = db_name(),
		PROCEDURE_SCHEM = user_name(o.uid),
		PROCEDURE_NAME = o.name +';'+ ltrim(str(p.number,5)),
		num_input_params = -1,      /* Constant since value unknown */
		num_output_params = -1,         /* Constant since value unknown */
		num_result_sets = -1,       /* Constant since value unknown */
		REMARKS = convert(varchar(254),null),   /* Remarks are NULL */
		PROCEDURE_TYPE = 0
	from sysobjects o, sysprocedures p,sysusers u
	where o.name like @sp_name ESCAPE '\'
		and p.sequence = 0
		and user_name(o.uid) like @sp_owner ESCAPE '\'
		and o.type = 'P'        /* Object type of Procedure */
		and p.id = o.id
		and u.uid = user_id()   /* constrain sysusers uid for use in 
								** subquery 
								*/
		and (suser_id() = 
				(select uid from sysusers where suid = suser_id()) /* User is the System Administrator */
			 or  o.uid = user_id()  /* User created the object */
						/* here's the magic..select the highest 
						** precedence of permissions in the 
						** order (user,group,public)  
						*/

			/*
			** The value of protecttype is
			**
			**      0  for grant with grant
			**      1  for grant and,
			**      2  for revoke
			**
			** As protecttype is of type tinyint, protecttype/2 is
			** integer division and will yield 0 for both types of
			** grants and will yield 1 for revoke, i.e., when
			** the value of protecttype is 2.  The XOR (^) operation
			** will reverse the bits and thus (protecttype/2)^1 will
			** yield a value of 1 for grants and will yield a
			** value of zero for revoke.
			**
			** Normal uids have values upto 16383, roles have uids
			** from 16384 upto 16389 and uids of groups start from
			** 16390 onwards.
			**
			** If there are several entries in the sysprotects table
			** with the same Object ID, then the following expression
			** will prefer an individual uid entry over a group entry
			** and prefer a group entry over a role entry.
			**
				** For example, let us say there are two users u1 and u2
			** with uids 4 and 5 respectiveley and both u1 and u2
			** belong to a group g12 whose uid is 16390.  procedure p1
			** is owned by user u0 and user u0 performs the following
			** actions:
			**
			**      grant exec on p1 to g12
			**      revoke grant on p1 from u1
			**
			** There will be two entries in sysprotects for the object
			** p1, one for the group g12 where protecttype = grant (1)
			** and one for u1 where protecttype = revoke (2).
			**
			** For the group g12, the following expression will
			** evaluate to:
			**
			**      (((+)*abs(16390-16383))*2) + ((1/2)^1))
			**      = ((14) + (0)^1) = 14 + 1 = 15
			**
			** For the user entry u1, it will evaluate to:
			**
			**      (((+)*abs(4-16383)*2) + ((2/2)^1))
			**      = ((abs(-16379)*2 + (1)^1)
			**      = 16379*2 + 0 = 32758
			**
			** As the expression evaluates to a bigger number for the
			** user entry u1, select max() will chose 32758 which,
			** ANDed with 1 gives 0, i.e., sp_jdbc_stored_procedures will
			** not display this particular procedure to the user.
			**
			** When the user u2 invokes sp_jdbc_stored_procedures, there is
			** only one entry for u2, which is the entry for the group
			** g12, and so this entry will be selected thus allowing
			** the procedure in question to be displayed.
			**
			** Notice that multiplying by 2 makes the number an
			** even number (meaning the last digit is 0) so what
			** matters at the end is (protecttype/2)^1.
			**
			*/

			or ((select distinct max(((sign(p.uid)*abs(p.uid-16383))*2)
					 + ((p.protecttype/2)^1))
			   from sysprotects p, sysusers u
					where action in (193,224)
					and u.uid = user_id()
					and (p.uid = 0         /* get rows for public */
					or p.uid = user_id()    /* current user */
					or p.uid = u.gid) 
					and p.id = o.id /* outer join to correlate 
								** with all rows in sysobjects 
								*/
			   )&1              /* more magic...normalize GRANT */
				  ) = 1)        /* final magic...compare Grants */
	order by PROCEDURE_SCHEM, PROCEDURE_NAME
end /* End of else of "if @version is not null" */
end /* End of "if @functions =0" */
else
begin
		select  FUNCTION_CAT = db_name(),
			FUNCTION_SCHEM  = user_name(o.uid),
			FUNCTION_NAME  = user_name(o.uid) + '.' + o.name,           
			REMARKS = convert(varchar(254),null),   /* Remarks are NULL */
			FUNCTION_TYPE  = 1, /*functionNoTable*/
			SPECIFIC_NAME = o.name +';'+ ltrim(str(p.number,5))
		from sysobjects o, sysprocedures p,sysusers u
		where o.name like @sp_name ESCAPE '\'
			and user_name(o.uid) like @sp_owner ESCAPE '\'
			and o.type = 'SF'       /* Object type of functions */
			and p.id = o.id
			and u.uid = user_id()       /* constrain sysusers uid for use in 
										** subquery 
										*/
			and p.sequence = 0

			and (suser_id() = 
					(select uid from sysusers where suid = suser_id()) /* User is the System Administrator */
				 or  o.uid = user_id()  /* User created the object */
							/* here's the magic..select the highest 
							** precedence of permissions in the 
							** order (user,group,public)  
							*/

				/* refer the logic for protecttype in the if part above */      
				or ((select max(((sign(p.uid)*abs(p.uid-16383))*2)
						 + ((p.protecttype/2)^1))
				   from sysprotects p, sysusers u
						where action in (193,224)
						and (p.uid = 0          /* get rows for public */
						or p.uid = user_id()    /* current user */
						or p.uid = u.gid) 
					and p.id = o.id     /* outer join to correlate 
										 ** with all rows in sysobjects 
										 */
				   )&1          /* more magic...normalize GRANT */
					  ) = 1)    /* final magic...compare Grants */
	order by FUNCTION_CAT, FUNCTION_SCHEM, FUNCTION_NAME, SPECIFIC_NAME
end /* End of else "if @functions =0" */
go
exec sp_procxmode 'sp_jdbc_stored_procedures', 'anymode'
go
grant execute on sp_jdbc_stored_procedures to public
go
commit
go
dump transaction sybsystemprocs with truncate_only 
go


/*
**  End of sp_jdbc_stored_procedures
*/

/* Don't delete the following line. This is where sp_jdbc_getprocedurecolumns gets inserted. */
/*** ADDPOINT_GETPROCEDURECOLUMNS ***/
/*
**  sp_jdbc_getprocedurecolumns
*/

set quoted_identifier off
go

/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects where name =
    'sp_jdbc_getprocedurecolumns')
    begin
        drop procedure sp_jdbc_getprocedurecolumns
    end
go
/** SECTION END: CLEANUP **/


create procedure sp_jdbc_getprocedurecolumns (
@sp_qualifier            varchar(32 ) = null,     /* stored procedure qualifier*/
@sp_owner                varchar(32 ) = null,     /* stored procedure owner */
@sp_name                 varchar(771),         /* stored procedure name */
@column_name             varchar(771) = null,
@parammetadata           int = 0,                 /* Is the call from getParamMetadata() ? */
@version                 int = null,               /* Comform to JDBC 4.0 spec if @version is not null */
@paramcolids             varchar(1000) = null,    /* parameter ids in the format 1,2,3...*/
@paramnames              varchar(1000) = null     /* parameter names in the format '@p0','@p1','@p2' */

/* @paramcolids Is this variable length enough?? */
/* @paramnames Is this variable length enough?? */
)
as
declare @msg                    varchar(250)
declare @group_num              int
declare @semi_position          int

declare @outer_select           varchar(350)
declare @from                   varchar(6)
declare @main_select_p1         varchar(1800)
declare @main_select_p2         varchar(1500)
declare @main_select_p3         varchar(3000)
declare @main_select_p4         varchar(2000) /* Is this variable length enough?? */
declare @union                  varchar(7)
declare @return_value_select_p1 varchar(1250)
declare @return_value_select_p2 varchar(2500)
declare @derivedTableName       varchar(20)
declare @orderby1               varchar(60)
declare @orderby2               varchar(40)
declare @orderCond              varchar(120)
declare @orderTable             varchar(2000) /* Is this variable length enough?? */
declare @char_bin_types         varchar(30)
declare @uni_types              varchar (10)

/* character and binary datatypes */
select @char_bin_types =
    char(47)+char(39)+char(45)+char(37)+char(35)+char(34)

    /* unichar, univarchar and unitext datatypes */
    /* Note that the actual type numbers are 155 (unichar), 135 (univarchar)
       and 174 (unitext), but because of issues that can arise when a server
       has utf-8 as the default charset and a non-binary sort order, we need
       to create a character string that is valid in utf-8. Therefore we 
       apply an offset of 60 to move the characters to be valid utf-8 chars.
       The stored proc later does a similar calculation to utilize these
       values properly */
    select @uni_types =
        char(95)+char(75)+char(114)


if @@trancount = 0
begin
    set chained off
end

set transaction isolation level 1
/* this will make sure that all rows are sent even if
** the client "set rowcount" is differect
*/

set rowcount 0

if @sp_qualifier is not null
begin
    if db_name() != @sp_qualifier
    begin
        if @sp_qualifier = ''
        begin
            select @sp_name = ''
            select @sp_owner = ''
        end
        else
        begin    
            /* 
            ** 18041, 'Stored Procedure qualifer must be name of
            ** current database'
            */
            exec sp_getmessage 18041, @msg out
            raiserror 18041 @msg
            return (1)
        end
    end
end
else
    select @sp_qualifier = db_name()

select @semi_position = charindex(';',@sp_name)
if (@semi_position > 0)
begin   /* If group number separator (;) found */
    select @group_num = convert(int,substring(@sp_name, @semi_position + 1, 2))
    select @sp_name = substring(@sp_name, 1, @semi_position -1)
end
else
begin   /* No group separator, so default to group number of 1 */
    select @group_num = 1
end      

if (@sp_owner is null) select @sp_owner ='%'
if (@sp_name is null) select @sp_name ='%'
if (@column_name is null) select @column_name ='%'

declare @colcount int
declare @tmpParamColids varchar(500)
declare @tmpParamNames varchar(500)
select @colcount = 1
select @tmpParamColids = @paramcolids
select @tmpParamNames = @paramnames

select @orderTable = '('
if(@tmpParamColids is not null and @tmpParamNames is not null)
begin
    WHILE (CHARINDEX(',', @tmpParamColids)>0)
     BEGIN
      select @orderTable = @orderTable + 'select colname=''' + 
                           SUBSTRING(@tmpParamColids, 1 , CHARINDEX(',', @tmpParamColids) - 1) + 
                           ''',colnumber=' + convert(varchar, @colcount) + ' union '

      select @tmpParamColids = SUBSTRING(@tmpParamColids, 
                               CHARINDEX(',', @tmpParamColids) + 1, LEN(@tmpParamColids))

      select @colcount = @colcount + 1
     END
    select @orderTable = @orderTable + 'select colname=''' + @tmpParamColids + 
                         ''',colnumber=' + convert(varchar,@colcount) + ' union '
    
    select @colcount = @colcount + 1
end

if(@tmpParamNames is not null)
begin    
    
    WHILE (CHARINDEX(',', @tmpParamNames)>0)
     BEGIN
      select @orderTable = @orderTable + 'select colname = ' + 
                           SUBSTRING(@tmpParamNames, 1 , CHARINDEX(',', @tmpParamNames) - 1) + 
                           ', colnumber = ' + convert(varchar, @colcount) + ' union '
        
      select @tmpParamNames = SUBSTRING(@tmpParamNames, 
                              CHARINDEX(',', @tmpParamNames) + 1, LEN(@tmpParamNames))
      
      select @colcount = @colcount + 1
     END
    select @orderTable = @orderTable + 'select colname = ' + @tmpParamNames + 
                         ', colnumber = ' + convert(varchar,@colcount)
end

select @orderTable = @orderTable + ')'

/*
 * select defined parameters (if any)
 */
select @outer_select = "select PROCEDURE_CAT, PROCEDURE_SCHEM, PROCEDURE_NAME, COLUMN_NAME, 
                        COLUMN_TYPE, DATA_TYPE, TYPE_NAME, PRECISION, LENGTH, SCALE,
                        RADIX, NULLABLE, REMARKS "
 if(@parammetadata = 0 and @version is not null)
    begin
        select @outer_select = @outer_select + " , COLUMN_DEF, SQL_DATA_TYPE, SQL_DATETIME_SUB, 
                                CHAR_OCTET_LENGTH, ORDINAL_POSITION, IS_NULLABLE, SPECIFIC_NAME "
    end
 if(@parammetadata = 1)
    begin
        select @outer_select = @outer_select + " , SS_DATA_TYPE "
     end
select @from= " from "

select @main_select_p1= "
SELECT DISTINCT
    PROCEDURE_CAT   = db_name(),
    PROCEDURE_SCHEM = user_name(o.uid),
    PROCEDURE_NAME  = (select object_name(p.id) + ';' + ltrim(str(p.number,5)) 
                      from sysprocedures p where p.id = o.id and p.number = @group_num 
                      group by p.id,p.number),
    COLUMN_NAME      = convert(varchar(255), c.name),"
                    /* Note that that ASE 12.5+ places a 2 in 
                       syscolumns.status2 (to signify an out-only param) when
                       a parameter is declared as an output param in a 
                       Transact-SQL stored proc. This is despite the fact that
                       a TSQL out param can be used for both input and output
                       values. The reason for this is that the status2 column
                       value is based on the text used to create the
                       procedure.
                       SQLJ procedures *do* have out-only params, as well as
                       inout params. Therefore, the status2 column for SQLJ
                       proc params will accurately reflect the paramter usage.
                       In any case, the below COLUMN_TYPE code accounts for
                       these quirks by differentiating between SQLJ procs
                       (the case branch with the 0x2000000 in it) and TSQL 
                       procs. */
select @main_select_p1 = @main_select_p1 + "COLUMN_TYPE     = case 
                       when c.status2 is NULL then 0
                       when (o.sysstat2 & hextoint('0x2000000') != 0) then
                           (ascii(substring('AD>B>>>E', c.status2, 1)) -64)
                       else (ascii(substring('AB>B>>>E', c.status2, 1))
                       - 64)
                      end, "

/* End of @main_select_p1 variable */

select @main_select_p2=
    "DATA_TYPE       = jdt.data_type,
     TYPE_NAME       = 
                        case
                            when t.name = 'usmallint' then 'unsigned smallint'
                            when t.name = 'uint' then 'unsigned int'
                            when t.name = 'ubigint' then 'unsigned bigint'
                        else
                            t.name
                        end,
    'PRECISION'     = (isnull(convert(int, c.prec),
                      isnull(convert(int, jdt.data_precision),
                      convert(int, c.length)))
                      +isnull(jdt.aux, convert(int,
                      ascii(substring('???AAAFFFCKFOLS',
                      2*(jdt.ss_dtype%35+1)+2-8/c.length,1))-60))),    
    LENGTH          = (isnull(convert(int, c.length), 
                      convert(int, jdt.length)) +
                      convert(int, isnull(jdt.aux,
                      ascii(substring('AAA<BB<DDDHJSPP',
                      2*(jdt.ss_dtype%35+1)+2-8/c.length,
                      1))-64))),
    SCALE           = (isnull(isnull(convert(smallint, c.scale), 
                      convert(smallint, jdt.numeric_scale)), 0) +
                      convert(smallint, isnull(jdt.aux,
                      ascii(substring('<<<<<<<<<<<<<<?',
                      2*(jdt.ss_dtype%35+1)+2-8/c.length,
                      1))-60))), "

/* End of @main_select_p2 variable */

select @main_select_p3= 
   "RADIX           = convert(smallint, 0), 
    NULLABLE        = case
                          when @parammetadata = 0 then convert(smallint, 2)
                          /* procedureNullableUnknown */
                      else
                          convert(smallint, convert(bit, c.status&8))
                          /* set nullability from status flag */
                      end,  
    REMARKS         = convert(varchar, c.printfmt),
    SS_DATA_TYPE    = convert(tinyint,jdt.ss_dtype),
    colid           = c.colid, /* parameter position order */
    COLUMN_DEF      = null,
    SQL_DATA_TYPE   = 0, /* future use */
    SQL_DATETIME_SUB = 0," /* future use */

            /* CHAR_OCTET_LENGTH */
            /*
            ** if the datatype is of type CHAR or BINARY
            ** then set char_octet_length to the same value
            ** assigned in the "prec" column.
            **
            ** The first part of the logic is:
            **
            **   if(c.type is in (155, 135, 47, 39, 45, 37, 35, 34))
            **       set char_octet_length = prec;
            **   else
            **       set char_octet_length = null;
            */
                /*
                ** check if in the list
                ** if so, return a 1 and multiply it by the precision 
                ** if not, return a null 
                */
select @main_select_p3 = @main_select_p3 + "  CHAR_OCTET_LENGTH = case 
                when 
                    convert(smallint, 
                    substring('0111111', 
                        charindex(char(c.type), @char_bin_types) +
                        charindex(char(c.type-60), @uni_types) + 1, 1)) = 0
                then null
                /* calculate the precision */
                else
                isnull(convert(int, c.prec),
                    isnull(convert(int, jdt.data_precision),
                        convert(int,c.length)))
                    +isnull(jdt.aux, convert(int,
                        ascii(substring('???AAAFFFCKFOLS',
                            2*(jdt.ss_dtype%35+1)+2-8/c.length,1))-60))
                end,


    ORDINAL_POSITION = c.colid,
    IS_NULLABLE = '',
    SPECIFIC_NAME = (select object_name(p.id) + ';' + ltrim(str(p.number,5))
                         from sysprocedures p where p.id = o.id and p.number = @group_num
                         group by p.id,p.number)
FROM syscolumns c, sysobjects o,
     sybsystemprocs.dbo.spt_jdbc_datatype_info jdt, systypes t

WHERE jdt.ss_dtype = c.type 
    and t.type = jdt.ss_dtype
    and c.usertype = t.usertype
    and c.id = o.id
    and user_name(o.uid) like @sp_owner ESCAPE '\'
    and o.type ='P'
    and o.name like @sp_name ESCAPE '\'
    and c.number = @group_num "
    
/* End of @main_select_p3 variable */

    if(@column_name is not null)
    begin
        select @main_select_p4 = " and c.name like @column_name ESCAPE '\' "
    end    
    
    if(@paramcolids is not null and @paramnames is not null)
    begin
    	select @main_select_p4 = @main_select_p4 + " and (c.colid in (" + @paramcolids +") OR c.name in (" + @paramnames +"))" 
    end
    else
    begin
    if(@paramnames is not null)
        begin
            select @main_select_p4 = @main_select_p4 + " and c.name in (" + @paramnames + ")"
        end
        else
        begin
        if(@paramcolids is not null)
            begin
               select @main_select_p4 = @main_select_p4 + " and c.colid in (" + @paramcolids + ")"
            end
        end
    end
    
if(@parammetadata = 0 or (@paramcolids is not null and substring(@paramcolids,1,1) = '0'))
begin
        select @union = " UNION "

        select @return_value_select_p1 ="
        /*
         * add the 'return parameter'
         */        
        SELECT DISTINCT
            PROCEDURE_CAT   = db_name(),
            PROCEDURE_SCHEM = user_name(o.uid),
            PROCEDURE_NAME  = (select object_name(p.id) + ';' + ltrim(str(p.number,5))
                              from sysprocedures p where p.id = o.id and p.number = @group_num
                              group by p.id,p.number),
            COLUMN_NAME     = convert(varchar,'RETURN_VALUE'),
            COLUMN_TYPE     = convert(smallint, case @parammetadata 
                                                    when 1 then 4 /* parameterModeOut */
                                                    else 5 /* procedureColumnReturn */
                                                end),
            DATA_TYPE       = jdt.data_type,
            TYPE_NAME       = jdt.type_name,
            'PRECISION'     = (isnull(convert(int, jdt.data_precision),
                              convert(int, jdt.length))
                              +isnull(jdt.aux, convert(int,
                              ascii(substring('???AAAFFFCKFOLS',
                              2*(jdt.ss_dtype%35+1)+2-8/jdt.length,1))-60))),"

        /* End of variable @return_value_select_p1 */

        select @return_value_select_p2 = "
            LENGTH          = (isnull(jdt.length, convert(int, t.length)) +
                              convert(int, isnull(jdt.aux,
                              ascii(substring('AAA<BB<DDDHJSPP',
                              2*(jdt.ss_dtype%35+1)+2-8/t.length,
                              1))-64))),
            SCALE           = (convert(smallint, jdt.numeric_scale) +
                              convert(smallint, isnull(jdt.aux,
                              ascii(substring('<<<<<<<<<<<<<<?',
                              2*(jdt.ss_dtype%35+1)+2-8/jdt.length,
                              1))-60))),    
            RADIX           = convert(smallint, 0), 
            NULLABLE        = convert(smallint, 0), /* procedureNoNulls */
            REMARKS         = convert(varchar, 'procedureColumnReturn'),
            SS_DATA_TYPE    = convert(tinyint,jdt.ss_dtype),
            colid           = 0, /* always the first parameter */
            COLUMN_DEF      = null,  /* Not stored in any system table */
            SQL_DATA_TYPE   = 0, /* Future use */
            SQL_DATETIME_SUB= 0, /* Future use */
            CHAR_OCTET_LENGTH = null, /* Integer type this shud be null */
            ORDINAL_POSITION = 0, /* return value */
            IS_NULLABLE = 'NO',
            SPECIFIC_NAME   = (select object_name(p.id) + ';' + ltrim(str(p.number,5))
                              from sysprocedures p where p.id = o.id and p.number = @group_num
                              group by p.id,p.number)
        FROM sybsystemprocs.dbo.spt_jdbc_datatype_info jdt, sysobjects o,systypes t
        WHERE jdt.ss_dtype = 56 /* return parameter is an int */
            and t.type = jdt.ss_dtype
            and user_name(o.uid) like @sp_owner ESCAPE '\'
            and o.type ='P'
            and o.name like @sp_name ESCAPE '\'
            and 'RETURN_VALUE' like @column_name ESCAPE '\' "
        /* End of variable return_value_select_p2 */

end
    
select @derivedTableName = " procedureColumns "

select @orderby1 =" ORDER BY PROCEDURE_SCHEM, PROCEDURE_NAME, colid "
select @orderCond = " where procedureColumns.COLUMN_NAME = orderTable.colname or convert(varchar, colid) = orderTable.colname "
select @orderby2 = " ORDER BY orderTable.colnumber"

if(@parammetadata = 0)
begin 
    execute(@outer_select + @from + '( ' + @main_select_p1 + 
    @main_select_p2 + @main_select_p3 + @main_select_p4 + @union + @return_value_select_p1 + 
    @return_value_select_p2 + ' ) '+ @derivedTableName + @orderby1)
end
else
begin
    if(@paramnames is not null)
    begin
        execute(@outer_select + @from + '( ' + @main_select_p1 + 
        @main_select_p2 + @main_select_p3 + @main_select_p4 + @union + @return_value_select_p1 + 
        @return_value_select_p2 + ' ) '+ @derivedTableName + ',' + @orderTable + 
        ' orderTable ' + @orderCond + @orderby2 )
    end
    else
    if(@paramcolids is not null)
    begin
        execute(@outer_select + @from + '( ' + @main_select_p1 + 
        @main_select_p2 + @main_select_p3 + @main_select_p4 + @union + @return_value_select_p1 + 
        @return_value_select_p2 + ' ) '+ @derivedTableName + @orderby1)
    end
    else
        select PROCEDURE_CAT='', PROCEDURE_SCHEM='', PROCEDURE_NAME='', COLUMN_NAME='',
        COLUMN_TYPE=0, DATA_TYPE=0, TYPE_NAME='', PRECISION=0, LENGTH=0, SCALE=0,
        RADIX=0, NULLABLE=0, REMARKS='',SS_DATA_TYPE=0 where 1=2
end        

go

exec sp_procxmode 'sp_jdbc_getprocedurecolumns', 'anymode'
go
grant execute on sp_jdbc_getprocedurecolumns to public
go
commit
go

set quoted_identifier on
go

/*
**  End of sp_jdbc_getprocedurecolumns
*/



/* Don't delete the following line. This is where sp_jdbc_getfunctioncolumns gets inserted. */
/*** ADDPOINT_GETFUNCTIONCOLUMNS ***/
/*
**  sp_jdbc_getfunctioncolumns
*/


/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects where name =
    'sp_jdbc_getfunctioncolumns')
    begin
        drop procedure sp_jdbc_getfunctioncolumns
    end
go
/** SECTION END: CLEANUP **/


create procedure sp_jdbc_getfunctioncolumns (
@fn_qualifier   varchar(32 ) = null,     /* function qualifier*/
@fn_owner       varchar(32 ) = null,     /* function owner */
@fn_name        varchar(771),         /* function name */
@column_name    varchar(771) = null)
as
declare @msg 			varchar(250)
declare @group_num              int
declare @semi_position          int
declare @char_bin_types   varchar(30)
declare @uni_types        varchar (10)

/* character and binary datatypes */
select @char_bin_types =
    char(47)+char(39)+char(45)+char(37)+char(35)+char(34)

    /* unichar, univarchar and unitext datatypes */
    /* Note that the actual type numbers are 155 (unichar), 135 (univarchar)
       and 174 (unitext), but because of issues that can arise when a server
       has utf-8 as the default charset and a non-binary sort order, we need
       to create a character string that is valid in utf-8. Therefore we 
       apply an offset of 60 to move the characters to be valid utf-8 chars.
       The stored proc later does a similar calculation to utilize these
       values properly */
    select @uni_types =
        char(95)+char(75)+char(114)


if @@trancount = 0
begin
	set chained off
end

set transaction isolation level 1
/* this will make sure that all rows are sent even if
** the client "set rowcount" is differect
*/

set rowcount 0



if @fn_qualifier is not null
begin
	if db_name() != @fn_qualifier
	begin
		if @fn_qualifier = ''
		begin
			select @fn_name = ''
			select @fn_owner = ''
		end
		else
		begin	
			/* 
			** 18041, 'Stored Procedure qualifer must be name of
			** current database'
			*/
			exec sp_getmessage 18041, @msg out
			raiserror 18041 @msg
			return (1)
		end
	end
end
else
	select @fn_qualifier = db_name()

select @semi_position = charindex(';',@fn_name)
if (@semi_position > 0)
begin   /* If group number separator (;) found */
    select @group_num = convert(int,substring(@fn_name, @semi_position + 1, 2))
    select @fn_name = substring(@fn_name, 1, @semi_position -1)
end
else
begin   /* No group separator, so default to group number of 0 */
    select @group_num = 0
end      

if (@fn_owner is null) select @fn_owner ='%'
if (@fn_name is null) select @fn_name ='%'
if (@column_name is null) select @column_name ='%'

/*
 * insert defined parameters (if any)
 */
SELECT FUNCTION_CAT, FUNCTION_SCHEM, FUNCTION_NAME, COLUMN_NAME, COLUMN_TYPE, DATA_TYPE,
TYPE_NAME, PRECISION, LENGTH, SCALE, RADIX, NULLABLE, REMARKS, CHAR_OCTET_LENGTH,
ORDINAL_POSITION, IS_NULLABLE, SPECIFIC_NAME FROM  
(SELECT DISTINCT
    FUNCTION_CAT    = db_name(),
    FUNCTION_SCHEM  = user_name(o.uid),
    FUNCTION_NAME    = (select user_name(o.uid) + '.' + object_name(p.id) 
                        from sysprocedures p where p.id = o.id and p.number = @group_num 
                        group by p.id,p.number),
    COLUMN_NAME      = c.name,
                    /* Note that that ASE 12.5+ places a 2 in 
                       syscolumns.status2 (to signify an out-only param) when
                       a parameter is declared as an output param in a 
                       Transact-SQL stored proc. This is despite the fact that
                       a TSQL out param can be used for both input and output
                       values. The reason for this is that the status2 column
                       value is based on the text used to create the
                       procedure.
                       SQLJ procedures *do* have out-only params, as well as
                       inout params. Therefore, the status2 column for SQLJ
                       proc params will accurately reflect the paramter usage.
                       In any case, the below COLUMN_TYPE code accounts for
                       these quirks by differentiating between SQLJ procs
                       (the case branch with the 0x2000000 in it) and TSQL 
                       procs. */

    COLUMN_TYPE     = case 
                       when c.status2 is NULL then 0
                       when (o.sysstat2 & hextoint('0x2000000') != 0) then
                           (ascii(substring('AD>B>>>E', c.status2, 1)) -64)
                       else (ascii(substring('AB>B>>>E', c.status2, 1))
                       - 64)
                      end,

    DATA_TYPE       = jdt.data_type,
    TYPE_NAME       = 
                        case
                            when t.name = 'usmallint' then 'unsigned smallint'
                            when t.name = 'uint' then 'unsigned int'
                            when t.name = 'ubigint' then 'unsigned bigint'
                        else
                            t.name
                        end,
    'PRECISION'     = (isnull(convert(int, c.prec),
                      isnull(convert(int, jdt.data_precision),
                      convert(int, c.length)))
                      +isnull(jdt.aux, convert(int,
                      ascii(substring('???AAAFFFCKFOLS',
                      2*(jdt.ss_dtype%35+1)+2-8/c.length,1))-60))),    
    LENGTH          = (isnull(convert(int, c.length), 
                      convert(int, jdt.length)) +
                      convert(int, isnull(jdt.aux,
                      ascii(substring('AAA<BB<DDDHJSPP',
                      2*(jdt.ss_dtype%35+1)+2-8/c.length,
                      1))-64))),
    SCALE           = (isnull(isnull(convert(smallint, c.scale), 
                       convert(smallint, jdt.numeric_scale)), 0) +
                        convert(smallint, isnull(jdt.aux,
                        ascii(substring('<<<<<<<<<<<<<<?',
                        2*(jdt.ss_dtype%35+1)+2-8/c.length,
                        1))-60))),    
    RADIX           = convert(smallint, 0), 
    NULLABLE        = convert(smallint, 2),/*functionNullableUnknown*/
    REMARKS         = c.printfmt,
            /* CHAR_OCTET_LENGTH */
            /*
            ** if the datatype is of type CHAR or BINARY
            ** then set char_octet_length to the same value
            ** assigned in the "prec" column.
            **
            ** The first part of the logic is:
            **
            **   if(c.type is in (155, 135, 47, 39, 45, 37, 35, 34))
            **       set char_octet_length = prec;
            **   else
            **       set char_octet_length = null;
            */
                /*
                ** check if in the list
                ** if so, return a 1 and multiply it by the precision 
                ** if not, return a null 
                */
    CHAR_OCTET_LENGTH = case 
                when 
                    convert(smallint, 
                    substring('0111111', 
                        charindex(char(c.type), @char_bin_types) +
                        charindex(char(c.type-60), @uni_types) + 1, 1)) = 0
                then null
                /* calculate the precision */
                else
                isnull(convert(int, c.prec),
                    isnull(convert(int, jdt.data_precision),
                        convert(int,c.length)))
                    +isnull(jdt.aux, convert(int,
                        ascii(substring('???AAAFFFCKFOLS',
                            2*(jdt.ss_dtype%35+1)+2-8/c.length,1))-60))
                end,

    ORDINAL_POSITION = c.colid, /* parameter position order */
    IS_NULLABLE = '',
    SPECIFIC_NAME = (select object_name(p.id) + ';' + ltrim(str(p.number,5)) 
                        from sysprocedures p where p.id = o.id and p.number = @group_num 
                        group by p.id,p.number)
FROM syscolumns c, sysobjects o,
     sybsystemprocs.dbo.spt_jdbc_datatype_info jdt, systypes t
WHERE jdt.ss_dtype = c.type 
    and t.type = jdt.ss_dtype
    and c.usertype = t.usertype
    and c.id = o.id
    and user_name(o.uid) like @fn_owner ESCAPE '\'
    and o.type ='SF'
    and o.name like @fn_name ESCAPE '\'
    and c.name like @column_name ESCAPE '\'
    and c.number = @group_num
    and c.name <> 'Return Type'
 /* selected all parameters */

UNION

/*
 * add the 'return parameter'
 */

 SELECT DISTINCT
    FUNCTION_CAT    = db_name(),
    FUNCTION_SCHEM  = user_name(o.uid),
    FUNCTION_NAME    = (select user_name(o.uid) + '.' + object_name(p.id) 
                        from sysprocedures p where p.id = o.id and p.number = @group_num 
                        group by p.id,p.number),
    COLUMN_NAME     = 'RETURN_VALUE',
    COLUMN_TYPE     = convert(smallint, 4), /* functionReturn */

    DATA_TYPE       = jdt.data_type,
    TYPE_NAME       = 
                        case
                            when t.name = 'usmallint' then 'unsigned smallint'
                            when t.name = 'uint' then 'unsigned int'
                            when t.name = 'ubigint' then 'unsigned bigint'
                        else
                            t.name
                        end,
    'PRECISION'     = (isnull(convert(int, c.prec),
                      isnull(convert(int, jdt.data_precision),
                      convert(int, c.length)))
                      +isnull(jdt.aux, convert(int,
                      ascii(substring('???AAAFFFCKFOLS',
                      2*(jdt.ss_dtype%35+1)+2-8/c.length,1))-60))),    
    LENGTH          = (isnull(convert(int, c.length), 
                      convert(int, jdt.length)) +
                      convert(int, isnull(jdt.aux,
                      ascii(substring('AAA<BB<DDDHJSPP',
                      2*(jdt.ss_dtype%35+1)+2-8/c.length,
                      1))-64))),
    SCALE           = (isnull(isnull(convert(smallint, c.scale), 
                       convert(smallint, jdt.numeric_scale)), 0) +
                        convert(smallint, isnull(jdt.aux,
                        ascii(substring('<<<<<<<<<<<<<<?',
                        2*(jdt.ss_dtype%35+1)+2-8/c.length,
                        1))-60))),    
    RADIX           = convert(smallint, 0), 
    NULLABLE        = convert(smallint, 2),/*functionNullableUnknown*/
    REMARKS         = c.printfmt,
            /* CHAR_OCTET_LENGTH */
            /*
            ** if the datatype is of type CHAR or BINARY
            ** then set char_octet_length to the same value
            ** assigned in the "prec" column.
            **
            ** The first part of the logic is:
            **
            **   if(c.type is in (155, 135, 47, 39, 45, 37, 35, 34))
            **       set char_octet_length = prec;
            **   else
            **       set char_octet_length = null;
            */
                /*
                ** check if in the list
                ** if so, return a 1 and multiply it by the precision 
                ** if not, return a null 
                */
    CHAR_OCTET_LENGTH = case 
                when 
                    convert(smallint, 
                    substring('0111111', 
                        charindex(char(c.type), @char_bin_types) +
                        charindex(char(c.type-60), @uni_types) + 1, 1)) = 0
                then null
                /* calculate the precision */
                else
                isnull(convert(int, c.prec),
                    isnull(convert(int, jdt.data_precision),
                        convert(int,c.length)))
                    +isnull(jdt.aux, convert(int,
                        ascii(substring('???AAAFFFCKFOLS',
                            2*(jdt.ss_dtype%35+1)+2-8/c.length,1))-60))
                end,


    ORDINAL_POSITION = 0, /* Return value is always first*/
    IS_NULLABLE = '',
    SPECIFIC_NAME = (select object_name(p.id) + ';' + ltrim(str(p.number,5)) 
                        from sysprocedures p where p.id = o.id and p.number = @group_num 
                        group by p.id,p.number)
FROM syscolumns c, sysobjects o,
     sybsystemprocs.dbo.spt_jdbc_datatype_info jdt, systypes t
WHERE jdt.ss_dtype = c.type 
    and t.type = jdt.ss_dtype
    and c.usertype = t.usertype
    and c.id = o.id
    and user_name(o.uid) like @fn_owner ESCAPE '\'
    and o.type ='SF'
    and o.name like @fn_name ESCAPE '\'
    and c.name like @column_name ESCAPE '\'
    and c.number = @group_num
    and c.name = 'Return Type'
    /* selected return parameter */
    ) temptable order by FUNCTION_CAT, FUNCTION_SCHEM, FUNCTION_NAME, SPECIFIC_NAME, ORDINAL_POSITION

go
exec sp_procxmode 'sp_jdbc_getfunctioncolumns', 'anymode'
go
grant execute on sp_jdbc_getfunctioncolumns to public
go
commit
go

/*
**  End of sp_jdbc_getfunctioncolumns
*/


/*
**  sp_jdbc_getversioncolumns
*/


/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects where name = 'sp_jdbc_getversioncolumns')
begin
	drop procedure sp_jdbc_getversioncolumns
end
go
/** SECTION END: CLEANUP **/

create procedure sp_jdbc_getversioncolumns (
				 @table_qualifier	varchar(32 ) = null,
				 @table_owner		varchar(32 ) = null,
				 @table_name		varchar(255))
as
	declare @indid			int
	declare @table_id		int
	declare @dbname			varchar(32 )
	declare @full_table_name	varchar(765)
	declare @msg			varchar(765)
	declare @owner			varchar(32 )

create table #versionhelp (SCOPE smallint null, COLUMN_NAME varchar(255) null,
DATA_TYPE int null, TYPE_NAME varchar(8) null, COLUMN_SIZE int null,
BUFFER_LENGTH smallint null, DECIMAL_DIGITS smallint null,  
PSEUDO_COLUMN smallint null)

	if @@trancount = 0
	begin
		set chained off
	end

	set transaction isolation level 1

		/* this will make sure that all rows are sent even if
		** the client "set rowcount" is differect
		*/

		set rowcount 0


	/* get database name */
	select @dbname = db_name()

	/* we don't want a temp table unless we're in tempdb */
	   /* Adding tempdb check here depending on the ASE version ADDTEMPDB */

   if (@table_name like '#%' and db_name() != db_name(tempdb_id()))
	begin	
		exec sp_getmessage 17676, @msg output
		raiserror 17676 @msg
		return (1)
	end

	if @table_qualifier is not null
	begin
		/* if qualifier doesn't match current database */
		if @dbname != @table_qualifier
		begin	
			exec sp_getmessage 18039, @msg output
			raiserror 18039 @msg
			return (1)
		end
	end

	if (@table_owner is null) select @table_owner = '%'
	else
	begin
		/*        
		** NOTE: SQL Server allows an underscore '_' in the table owner, even 
		**       though it is a single character wildcard.
		*/
		if (charindex('%',@table_owner) > 0)
		begin
			exec sp_getmessage 17993, @msg output
			raiserror 17993 @msg, @table_owner
			return(1)
		end
		exec sp_jdbc_escapeliteralforlike @table_owner output
	end

	if (@table_name is null) 
	begin
	   exec sp_getmessage 17993, @msg output
	   raiserror 17993 @msg, 'NULL'
	   return(1)
	end

	if (select count(*) 
		from sysobjects
		where user_name(uid) 
		like @table_owner ESCAPE '\'
		and name = @table_name) = 0 
	begin
	  exec sp_getmessage 17674, @msg output
	  raiserror 17674 @msg
	  return 1
	end
	else 
	begin
	declare version_cur cursor for
				select @table_owner = user_name(uid) from sysobjects 
			where name = @table_name and user_name(uid) like @table_owner

	open version_cur
	fetch version_cur into @owner

	while (@@sqlstatus = 0)
		begin
			if @owner is null
		begin	/* if unqualified table name */
		select @full_table_name = @table_name
		end
		else
		begin	/* qualified table name */
		select @full_table_name = @owner + '.' + @table_name
		end

		/* get object ID */
		select @table_id = object_id(@full_table_name)

		insert into #versionhelp select
		convert(smallint, 0),
		c.name ,
		(select data_type from 
			sybsystemprocs.dbo.spt_jdbc_datatype_info
			where type_name = 'binary'),
		'BINARY',
		isnull(d.data_precision,
				convert(int,c.length))
				+ isnull(d.aux, convert(int,
				ascii(substring('???AAAFFFCKFOLS',
				2*(d.ss_dtype%35+1)+2-8/c.length,1))
				-60)),
		18, /* Number of chars = 2^4 byte + '0x' */
		isnull(d.numeric_scale + convert(smallint,
				isnull(d.aux,
				ascii(substring('<<<<<<<<<<<<<<?',
				2*(d.ss_dtype%35+1)+2-8/c.length, 1))
				-60)),0),
		1
		from
			systypes t, syscolumns c, 
			sybsystemprocs.dbo.spt_jdbc_datatype_info d
		where
			c.id = @table_id
			and c.type = d.ss_dtype
			and c.usertype = 80	/* TIMESTAMP */
			and t.usertype = 80	/* TIMESTAMP */
		fetch version_cur into @owner
	end
	end
	select * from #versionhelp
go
exec sp_procxmode 'sp_jdbc_getversioncolumns', 'anymode'
go
grant execute on sp_jdbc_getversioncolumns to public
go
commit
go
dump transaction sybsystemprocs with truncate_only 
go

/*
**  End of sp_jdbc_getversioncolumns
*/



/*
**  sp_default_charset
*/

/* 
** obtain the SQL server default charset
*/


/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects where name = 'sp_default_charset')
begin
		drop procedure sp_default_charset
end
go
/** SECTION END: CLEANUP **/


/*
**  create a procedure that will query the datasource
**  specific syscharset, and sysconfigures tables, and do a join to 
**  determine what is the correct charset that has been set as a default
**  on the server.
*/
create procedure sp_default_charset
as

	if @@trancount = 0
	begin
		set chained off
	end

	set transaction isolation level 1

	select name as DEFAULT_CHARSET from master.dbo.syscharsets
	   where ((select value from master.dbo.sysconfigures      
			   where config=131)  /* default charset id */
			  = master.dbo.syscharsets.id)
go
exec sp_procxmode 'sp_default_charset', 'anymode'
go
grant execute on sp_default_charset to public
go
dump transaction sybsystemprocs with truncate_only 
go

/*
**  End of sp_default_charset
*/


/* 
** JDBC 2.0
** 
** DatabaseMetaData.getUDTs(catalog, schema, typeNamePattern, int types[])
**
** NOT SUPPORTED
*/
/** SECTION BEGIN: CLEANUP **/
if exists (select * from sysobjects where name = 'sp_jdbc_getudts')
begin
		drop procedure sp_jdbc_getudts
end
go
/** SECTION END: CLEANUP **/
create procedure sp_jdbc_getudts (
		@table_qualifier        varchar(32 ) = NULL,
		@table_owner            varchar(32 ) = NULL,
		@type_name_pattern      varchar(255),
		@types                  varchar(255))
as
	declare @empty_string varchar(1)
	declare @empty_int int

	select @empty_string = ''
	select @empty_int = 0

	/* not supported, return an empty result set */    
	select 
		TYPE_CAT = @empty_string,
		TYPE_SCHEM = @empty_string,
		TYPE_NAME = @empty_string,
		CLASS_NAME = @empty_string,
		DATA_TYPE = @empty_int,
		REMARKS = @empty_string
	where
		1 = 2
go

/* end of dbo.sp_jdbc_getudts */
exec sp_procxmode 'sp_jdbc_getudts', 'anymode'
go
grant execute on sp_jdbc_getudts to public
go
dump transaction sybsystemprocs with truncate_only 
go


/* Don't delete the following line. This is where sp_jdbc_getsupertypes gets inserted. */
/*** ADDPOINT_GETSUPERTYPES ***/
/*
** JDBC 3.0
**
** DatabaseMetaData.getSuperTypes(catalog, schemaPattern, typeNamePattern)
**
*/

/** SECTION BEGIN: CLEANUP **/

if exists (select * from sysobjects where name = 'sp_jdbc_getsupertypes')
begin
        drop procedure sp_jdbc_getsupertypes
end
go
/** SECTION END: CLEANUP **/

create procedure sp_jdbc_getsupertypes (
        @catalog              varchar(32) = NULL,
        @schemaPattern        varchar(32) = NULL,
        @typeNamePattern      varchar(255))        
as
    if (@catalog is null) or (@catalog= '')
        select @catalog = db_name()

    if (@schemaPattern is null) or (@schemaPattern= '')
        select @schemaPattern = '%'
    
    if (@typeNamePattern is null)
    begin
        raiserror 17208  
            'Null is not allowed for parameter TYPE NAME PATTERN'
        return(1)
    end    
    
    select 
        TYPE_CAT        = @catalog,
        TYPE_SCHEM      = convert(varchar, b.name),
        TYPE_NAME       = convert(varchar, t.name),
        SUPERTYPE_CAT   = @catalog,
        SUPERTYPE_SCHEM = 'dbo',
        SUPERTYPE_NAME  = convert(varchar, p.name)

    from systypes t, sysusers b, systypes p 
    where t.uid = b.uid 
        and t.usertype >= 100
        and t.type = p.type 
        and p.usertype = (select min(usertype) from systypes where type = t.type ) 
        and b.name like @schemaPattern 
        and t.name like @typeNamePattern
go
exec sp_procxmode 'sp_jdbc_getsupertypes', 'anymode'
go
grant execute on sp_jdbc_getsupertypes to public
go
dump transaction sybsystemprocs with truncate_only 
go

/* end of dbo.sp_jdbc_getsupertypes */


/* Don't delete the following line. This is where sp_jdbc_getsupertables get inserted. */
/*** ADDPOINT_SUPERTABLES ***/

/* 
** JDBC 3.0
** 
** DatabaseMetaData.getSuperTables(catalog, schemaPattern, tableNamePattern)
**
*/

/** SECTION BEGIN: CLEANUP **/

if exists (select * from sysobjects where name = 'sp_jdbc_getsupertables')
begin
        drop procedure sp_jdbc_getsupertables
end
go

/** SECTION END: CLEANUP **/

create procedure sp_jdbc_getsupertables (
        @table_qualifier        varchar(32) = NULL,
        @table_owner            varchar(32),
        @table_name_pattern     varchar(255))
        
as
    declare @empty_string varchar(1)
       
    select @empty_string = '' 

    /* Return an empty result set */    
    select 
        TABLE_CAT = convert(varchar, @empty_string),
        TABLE_SCHEM = convert(varchar, @empty_string),
        TABLE_NAME = convert(varchar, @empty_string),
        SUPERTABLE_NAME = convert(varchar, @empty_string)
    where
        1 = 2
go

exec sp_procxmode 'sp_jdbc_getsupertables', 'anymode'
go
grant execute on sp_jdbc_getsupertables to public
go
dump transaction sybsystemprocs with truncate_only 
go

/* end of dbo.sp_jdbc_getsupertables */



/* Don't delete the following line. This is where sp_jdbc_getattributes get inserted. */
/*** ADDPOINT_ATTRIBUTES ***/

/* 
** JDBC 3.0
** 
** DatabaseMetaData.getAttributes(catalog, schemaPattern, typeNamePattern, attributeNamePattern)
**
*/

/** SECTION BEGIN: CLEANUP **/

if exists (select * from sysobjects where name = 'sp_jdbc_getattributes')
begin
        drop procedure sp_jdbc_getattributes
end
go

/** SECTION END: CLEANUP **/

create procedure sp_jdbc_getattributes (
        @attribute_qualifier     varchar(32) = NULL,
        @attribute_owner         varchar(32) = NULL,
        @type_name_pattern       varchar(255)= NULL,
        @attribute_name_pattern  varchar(255)= NULL)
        
as
    declare @empty_string varchar(1)
       
    select @empty_string = '' 

    /* Return an empty result set */    
    select 
        TYPE_CAT = convert(varchar, @empty_string),
        TYPE_SCHEM = convert(varchar, @empty_string),
        TYPE_NAME = convert(varchar, @empty_string),
        ATTR_NAME = convert(varchar, @empty_string),
        DATA_TYPE = convert(int, 0),
        ATTR_TYPE_NAME = convert(varchar, @empty_string),
        ATTR_SIZE = convert(int, 0),
        DECIMAL_DIGITS = convert(int, 0),
        NUM_PREC_RADIX = convert(int, 0),
        NULLABLE = convert(int, 2),
        REMARKS = convert(varchar, @empty_string),
        ATTR_DEF = convert(varchar, @empty_string),
        SQL_DATA_TYPE = convert(int, 0),
        SQL_DATETIME_SUB = convert(int, 0),
        CHAR_OCTET_LENGTH = convert(int, 0),
        ORDINAL_POSITION = convert(int, 0),
        IS_NULLABLE = convert(varchar, @empty_string),
        SCOPE_CATALOG = convert(varchar, @empty_string),
        SCOPE_SCHEMA = convert(varchar, @empty_string),
        SCOPE_TABLE = convert(varchar, @empty_string),
        SOURCE_DATA_TYPE = convert(smallint, 0)
    where
        1 = 2
go

exec sp_procxmode 'sp_jdbc_getattributes', 'anymode'
go
grant execute on sp_jdbc_getattributes to public
go
dump transaction sybsystemprocs with truncate_only 
go

/* end of dbo.sp_jdbc_getattributes */



/** SECTION BEGIN: CLEANUP **/
if exists (select * from sysobjects where name = 'sp_jdbc_getxacoordinator')
begin
		drop procedure sp_jdbc_getxacoordinator
end
go
/** SECTION END: CLEANUP **/       

/* Don't delete the following line. This is where sp_jdbc_getxacoordinator gets inserted. */
/*** ADDPOINT_JTA ***/
/*
** JDBC 2.0 extensions (JTA support)
** 
** SybDatabaseMetaData.getXACoordinatorType()
** 
** returns a resultset of the form:
** TxnStyle (indicating which transaction coordinator is being used.
** 0 means none.
**
** RequiredRoles (indicates what role the user must have to use)
** NULL means none.
**
** Status (a bitmask of capabilities that the coordinator provides)
** 0x00000001 - set if user has necessary role
** 0x00000002 - set if txns can migrate among connections
** 0x00000004 - set if multiple connections can participate in same txn simultaneously
**
** UniqueID (a string which uniquely identifies this server)
*/
create procedure sp_jdbc_getxacoordinator
as

declare @txnMode int
declare @status int
declare @uniqueID varchar(12)
declare @curvalue int
declare @sqlCurValue varchar(200)
/* For 12.0 and higher, jConnect expects to use DTM001. */
/* DTM001 is defined as 2 in SybDataSource.RMTYPE_ASE_XA_DTM. */
/* Users must have dtm_tm_role to use DTM.   */
/* Next, determine whether or not user has */
/* dtm_tm_role when setting the status bits. */
/* Lastly, return the unique ID for this resource. */

/* verify that the 15.0 server is configured for XA using DTM */

select @sqlCurValue = 'select @curvalue=cur.value from master.dbo.syscurconfigs cur, master.dbo.sysconfigures conf
     where conf.comment = ''enable DTM'' and cur.config = conf.config '
/* The columns syscurconfigs.instanceid is present only if server is in sdc mode'*/
if(@@clustermode = 'shared disk cluster')
begin
    select @sqlCurValue = @sqlCurValue + 'and cur.instanceid=@@instanceid'
end 

execute(@sqlCurValue)

if (@curvalue != 0)
begin
     /* verify that the 12.0 server is licensed for XA using DTM */
     if (license_enabled('ASE_DTM') = 1)
     begin
         select @txnMode=2
     end
end
else
begin
     select @txnMode=0
end

/* now check for role permission */
if (charindex('dtm_tm_role', show_role()) != 0)
begin
     select @status=7
end
else
begin
     select @status=6
end

/* return results and name the columns */
select TxnStyle=@txnMode, RequiredRole='dtm_tm_role', Status=@status, UniqueID=@@nodeid
go

/* end of dbo.sp_jdbc_getxacoordinator */
exec sp_procxmode 'sp_jdbc_getxacoordinator', 'anymode'
go
grant execute on sp_jdbc_getxacoordinator to public
go
dump transaction sybsystemprocs with truncate_only 
go

/* Don't delete the following line. This is where server-specific sp's get inserted. */
/*** ADDPOINT_DCL ***/
/*
**  sp_jdbc_class_for_name
*/

/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects where name =
    'sp_jdbc_class_for_name')
    begin
        drop procedure sp_jdbc_class_for_name
    end
go
/** SECTION END: CLEANUP **/

create procedure sp_jdbc_class_for_name (
        @class_name varchar(255))
as
    select
        xtbinaryoffrow
    from 
        sysxtypes 
    where 
        xtname = @class_name

go

exec sp_procxmode 'sp_jdbc_class_for_name', 'anymode'
go
grant execute on sp_jdbc_class_for_name to public
go
dump transaction sybsystemprocs with truncate_only 
go

/*
**  End of sp_jdbc_class_for_name
*/



/*
**  sp_jdbc_jar_for_class
*/

/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects where name =
    'sp_jdbc_jar_for_class')
    begin
        drop procedure sp_jdbc_jar_for_class
    end
go
/** SECTION END: CLEANUP **/

create procedure sp_jdbc_jar_for_class (
        @class_name varchar(255))
as
    select
        sj.jbinary
    from
        sysjars sj, sysxtypes sjc
    where
        sjc.xtname = @class_name and sj.jid = sjc.xtcontainer
go

exec sp_procxmode 'sp_jdbc_jar_for_class', 'anymode'
go
grant execute on sp_jdbc_jar_for_class to public
go
dump transaction sybsystemprocs with truncate_only 
go

/*
**  End of sp_jdbc_jar_for_class
*/



/*
**  sp_jdbc_jar_by_name
*/

/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects where name =
    'sp_jdbc_jar_by_name')
    begin
        drop procedure sp_jdbc_jar_by_name
    end
go
/** SECTION END: CLEANUP **/

create procedure sp_jdbc_jar_by_name (
        @jar_name varchar(255))
as
    select
        sj.jbinary
    from
        sysjars sj
    where 
        jname = @jar_name
go

exec sp_procxmode 'sp_jdbc_jar_by_name', 'anymode'
go
grant execute on sp_jdbc_jar_by_name to public
go
dump transaction sybsystemprocs with truncate_only 
go

/*
**  End of sp_jdbc_jar_by_name
*/



/*
**  sp_jdbc_classes_in_jar
*/

/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

if exists (select * from sysobjects where name =
    'sp_jdbc_classes_in_jar')
    begin
        drop procedure sp_jdbc_classes_in_jar
    end
go
/** SECTION END: CLEANUP **/

create procedure sp_jdbc_classes_in_jar (
        @jar_name varchar(255))
as
    select 
        sjc.xtname 
    from 
        sysjars sj, sysxtypes sjc 
    where 
        sj.jid = sjc.xtcontainer and sj.jname = @jar_name
go

exec sp_procxmode 'sp_jdbc_classes_in_jar', 'anymode'
go
grant execute on sp_jdbc_classes_in_jar to public
go
dump transaction sybsystemprocs with truncate_only 
go

/*
**  End of sp_jdbc_classes_in_jar
*/



/** SECTION BEGIN: CLEANUP **/
use sybsystemprocs 
go

sp_configure 'allow updates', 0
go

set quoted_identifier off
go

/** SECTION END: CLEANUP **/


/*
**  End of sql_server.sql
*/
declare @retval int
exec @retval = sp_version 'installjdbc',NULL,'jConnect (TM) for JDBC(TM)/7.07 ESD #5 (Build 26792)/P/EBF20686/JDK 1.6.0/jdbcmain/OPT/Mon Oct 15 11:36:14 PDT 2012', 'end'
if (@retval != 0) select syb_quit()
go




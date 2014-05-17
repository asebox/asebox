/*
**      Sybase DBN-LIBRARY 
**      Confidential Property of Sybase, Inc.
**      (c) Copyright Sybase, Inc. 2005 - 2006
**      All rights reserved.
**
**
** Use, duplication, or disclosure by the Government
** is subject to restrictions as set forth in subparagraph (c) (1) (ii)
** of the Rights in Technical Data and Computer Software clause
** at DFARS 52.227-7013. Sybase, Inc. One Sybase Drive, Dublin, CA 94568,
** USA.
**
** History
**
** 001  09SEP05	 Created for MIT Kerberos Support.			steng
** 002  05JAN06	 Reserved tokens for security features of
**               MIT Kerberos integrate and confidentility.		steng
*/

#ifndef __sybdbn__
#define __sybdbn__

#include <sybdb.h>

/*
** Macros to set security services in the LOGINREC structure.
*/
#define DBSETNETWORKAUTH           101
#define DBSETMUTUALAUTH            102
#define DBSETSERVERPRINCIPAL       103

#define DBSETLNETWORKAUTH(a,b)     dbsetlsecserv((a), (b), DBSETNETWORKAUTH)
#define DBSETLMUTUALAUTH(a,b)      dbsetlsecserv((a), (b), DBSETMUTUALAUTH)
#define DBSETLSERVERPRINCIPAL(a,b) dbsetlsecname((a), (b), DBSETSERVERPRINCIPAL)

/*
** DB-Library does not support integrity and confidentiality features yet.
** Reserved the tokens of the others MIT Kerberos security features for
** future development upon requested.
*/
#define DBSETINTEGRITY             104
#define DBSETDETECTSEQ             105
#define DBSETDETECTREPLAY          106
#define DBSETCONFIDENTIALITY       107

#define DBSETLINTEGRITY(a,b)       dbsetlsecserv((a), (b), DBSETINTEGRITY)
#define DBSETLDETECTSEQ(a,b)       dbsetlsecserv((a), (b), DBSETDETECTSEQ)
#define DBSETLDETECTREPLAY(a,b)    dbsetlsecserv((a), (b), DBSETDETECTREPLAY)
#define DBSETLCONFIDENTIALITY(a,b) dbsetlsecserv((a), (b), DBSETCONFIDENTIALITY)

/*
** Function prototypes for all public functions
*/
RETCODE CS_PUBLIC dbsetlsecserv  PROTOTYPE((
	LOGINREC DBFAR *lptr,
	int value,
	int type
	));
RETCODE CS_PUBLIC dbsetlsecname  PROTOTYPE((
	LOGINREC DBFAR *lptr,
	char DBFAR *name,
	int type
	));

#endif /*  __sybdbn__ */

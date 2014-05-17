      * SYBTESQL.CBL
      *    contains the SQL--PROLOG and SQL--EPILOG paragraphs
      *    this file will be copied into the target file via the
      *    the Cobol COPY function
      *
      ************************************************************
      * SQL--PROTECT.
      ************************************************************
        SQL--PROTECT.
      *  This paragraph prevents unintentional serial execution of
      * the rest of the ESQL paragraphs (if the last paragraph of 
      * the embedded program does not stop run or something.
            GO TO SQL--LAST.
      ************************************************************
      * SQL--PROLOG
      ************************************************************
        SQL--PROLOG.
            MOVE CS-SUCCEED TO SQL--RETCODE
            CALL "CSBCTXGLOBAL" USING SQL--CS-VERSION
                     SQL--RETCODE SQL--CTX OF SQL--HANDLES
            IF SQL--RETCODE NOT EQUAL CS-SUCCEED
                MOVE 25001 TO SQL--INTRERR
                PERFORM SQL--CTXERR
            END-IF
      * Clear the cs-diag messages first
      * SQL--CSBDIAG performs SQL--CTXERR if necesary 
            IF ((SQL--RETCODE EQUAL CS-SUCCEED) AND
                (SQL--STMTTYPE NOT EQUAL 
                SQL-GET-DIAGNOSTICS) AND
                (SQL--STMTTYPE NOT EQUAL 
                SQL-SET-DIAGNOSTIC-COUNT))
                MOVE CS-CLEAR TO SQL--OPERATION
                MOVE CS-UNUSED TO SQL--ERRINDEX
                PERFORM SQL--CSBDIAG
                IF (SQL--RETCODE NOT EQUAL CS-SUCCEED)
      * If error CSBDIAG -> Unrecoverable error or internal
                    MOVE 25002 TO SQL--INTRERR
                    PERFORM SQL--CTXERR
                END-IF
            END-IF
      * Initialize ct_lib -- if it has already been initialized,
      *   CTBINIT will return right away
            IF (SQL--RETCODE EQUAL CS-SUCCEED)
                CALL "CTBINIT" USING
                    SQL--CTX OF SQL--HANDLES
                    SQL--RETCODE
                    SQL--CS-VERSION
      * CTBINIT handles own errors
                IF (SQL--RETCODE NOT EQUAL CS-SUCCEED)
                    PERFORM SQL--EPILOG
                    MOVE CS-FAIL TO SQL--RETCODE
                END-IF
            END-IF
      * We continue only if we have succeeded in Context and CTLib
      * initialization.  Otherwise we exit (without EPILOG)
      * Now we can check statement type validity (after CSBDIAG in
      * case we need to call SQL--SETINTRERR)
            IF (SQL--RETCODE EQUAL CS-SUCCEED) 
                IF
                (SQL--STMTTYPE OF SQL--HANDLES LESS THAN 
                    UNKNOWN-STMT) OR
                    (SQL--STMTTYPE OF SQL--HANDLES EQUAL 
                    UNKNOWN-STMT) OR
                    (SQL--STMTTYPE OF SQL--HANDLES GREATER THAN 
                    MAX-SQL-STMT)
                    MOVE 25002 TO SQL--INTRERR
                    PERFORM SQL--SETINTRERR
                    MOVE CS-FAIL TO SQL--RETCODE
                END-IF 
      * If we are in a threaded environment,
      * we need to set threadid in the current-connection,
      * connection-name, cursor-name, and dynamic-statement-name
      * cs-objname structures, as they are scoped separately for
      * threads
                IF (SQL--RETCODE EQUAL CS-SUCCEED)
                    CALL "SQLTHRED" USING
                        SQL--CTX OF SQL--HANDLES
                        SQL--RETCODE 
                        SQL--THREAD OF SQL--CONNNAME OF 
                            SQL--HANDLES
                        SQL--THREADLEN OF SQL--CONNNAME OF 
                            SQL--HANDLES
                    CALL "SQLTHRED" USING
                        SQL--CTX OF SQL--HANDLES
                        SQL--RETCODE 
                        SQL--THREAD OF SQL--CURRENTNAME OF 
                            SQL--HANDLES
                        SQL--THREADLEN OF SQL--CURRENTNAME OF 
                            SQL--HANDLES
                    CALL "SQLTHRED" USING
                        SQL--CTX OF SQL--HANDLES
                        SQL--RETCODE 
                        SQL--THREAD OF SQL--STMTNAME OF 
                            SQL--HANDLES
                        SQL--THREADLEN OF SQL--STMTNAME OF 
                            SQL--HANDLES
                    CALL "SQLTHRED" USING
                        SQL--CTX OF SQL--HANDLES
                        SQL--RETCODE 
                        SQL--THREAD OF SQL--CURNAME OF 
                            SQL--HANDLES
                        SQL--THREADLEN OF SQL--CURNAME OF 
                            SQL--HANDLES
                    CALL "SQLTHRED" USING
                        SQL--CTX OF SQL--HANDLES
                        SQL--RETCODE 
                        SQL--THREAD OF SQL--STMT-CUR-NAME OF 
                            SQL--HANDLES
                        SQL--THREADLEN OF SQL--STMT-CUR-NAME OF 
                            SQL--HANDLES
                    CALL "SQLTHRED" USING
                        SQL--CTX OF SQL--HANDLES
                        SQL--RETCODE 
                        SQL--THREAD OF SQL--STMT-CMD-NAME OF 
                            SQL--HANDLES
                        SQL--THREADLEN OF SQL--STMT-CMD-NAME OF 
                            SQL--HANDLES
                    CALL "SQLTHRED" USING
                        SQL--CTX OF SQL--HANDLES
                        SQL--RETCODE 
                        SQL--THREAD OF SQL--DESC-CONN-NAME OF 
                            SQL--HANDLES
                        SQL--THREADLEN OF SQL--DESC-CONN-NAME OF 
                            SQL--HANDLES
                END-IF
    
      * Get the connection info for specified or 'current' connection
      * If the connection-name is not specified, get current
      * need special connection for Dynamic descriptors
      * No connection needed for DISCONNECT ALL , INIT and EXIT
                IF ((SQL--RETCODE EQUAL CS-SUCCEED) AND
                    (SQL--STMTTYPE OF SQL--HANDLES EQUAL 
                    SQL-ALLOC-DESC) OR 
		    (SQL--STMTTYPE OF SQL--HANDLES EQUAL 
                    SQL-SET-DESCRIPTOR) OR 
		    (SQL--STMTTYPE OF SQL--HANDLES EQUAL 
                    SQL-GET-DESCRIPTOR) OR
                    (SQL--STMTTYPE OF SQL--HANDLES EQUAL 
                    SQL-DEALLOCATE-DESCRIPTOR))
                    PERFORM SQL--DESCCONN
                ELSE IF ((SQL--STMTTYPE OF SQL--HANDLES NOT EQUAL 
                    SQL-DISCONNECT-ALL) AND
                    (SQL--STMTTYPE OF SQL--HANDLES NOT EQUAL 
                    SQL-INIT-STMT) AND
                    (SQL--STMTTYPE OF SQL--HANDLES NOT EQUAL 
                    SQL-EXIT-STMT))
      * Connections should exist for statements other than connect/
      * disconnect
                        IF SQL--STMTTYPE OF SQL--HANDLES EQUAL 
                            SQL-ANSI-CONNECT
                            OR SQL--STMTTYPE OF SQL--HANDLES EQUAL 
                            SQL-NONANSI-CONNECT
                            MOVE CS-FALSE TO SQL--THINKEXISTS OF 
                            SQL--CONNNAME OF SQL--HANDLES 
                        ELSE
                            MOVE CS-TRUE TO SQL--THINKEXISTS OF 
                                SQL--CONNNAME OF SQL--HANDLES 
                        END-IF
                        IF (SQL--LNLEN OF SQL--CONNNAME OF 
                            SQL--HANDLES EQUAL CS-UNUSED)
                            MOVE CS-TRUE TO SQL--ISCURRENT
                        END-IF
                        MOVE SQL--GET TO SQL--ACTION
                        PERFORM SQL--CONNOP
                    ELSE
      * Disconnect all does not require any connections
                        MOVE SQL--NULL-CONNECTION TO SQL--CONNECTION 
                            OF SQL--CONN OF SQL--HANDLES
                    END-IF
                END-IF
      * Now go after the object we are really working on
                IF SQL--RETCODE EQUAL CS-SUCCEED
                    EVALUATE SQL--STMTTYPE OF SQL--HANDLES 
      * Handle each statement type
      * connections
                    WHEN SQL-ANSI-CONNECT
                    WHEN SQL-NONANSI-CONNECT
                        MOVE SQL--CREATE TO SQL--ACTION
                        PERFORM SQL--CONNOP
                    WHEN SQL-SET-CONNECTION
      * code generated in previous versions
                        MOVE CS-FALSE TO SQL--THINKEXISTS OF 
                            SQL--CURRENTNAME OF SQL--HANDLES
                        MOVE SQL--LNLEN OF SQL--CONNNAME OF 
                            SQL--HANDLES TO
                            SQL--BUFLEN OF SQL--CURRENT OF 
                            SQL--HANDLES
                        CALL "CSBOBJECTS" USING SQL--CTX OF 
                            SQL--HANDLES SQL--RETCODE CS-CLEAR 
                            SQL--CURRENTNAME OF
                            SQL--HANDLES SQL--DUMMY SQL--DUMMY 
                            SQL--CURRENT OF
                            SQL--HANDLES SQL--LAST-NAME OF 
                            SQL--CONNNAME OF SQL--HANDLES
                        CALL "CSBOBJECTS" USING SQL--CTX OF 
                            SQL--HANDLES
                            SQL--RETCODE CS-SET SQL--CURRENTNAME OF
                            SQL--HANDLES SQL--DUMMY SQL--DUMMY 
                            SQL--CURRENT OF
                            SQL--HANDLES SQL--LAST-NAME OF 
                            SQL--CONNNAME OF SQL--HANDLES
                    WHEN SQL-DISCONNECT
                    WHEN SQL-DISCONNECT-ALL
                        MOVE SQL--DESTROY TO SQL--ACTION
                        PERFORM SQL--CONNOP
      * If dropping CURRENT or all connection , remove current 
      * connect-id from cs_objects; if dropping all conns, current 
      * may not exist
                        IF  SQL--RETCODE EQUAL CS-SUCCEED AND
                            (SQL--ISCURRENT EQUAL CS-TRUE
                            OR SQL--STMTTYPE EQUAL SQL-DISCONNECT-ALL)
                            MOVE SQL--LNLEN OF SQL--CONNNAME OF 
                                 SQL--HANDLES 
                                 TO SQL--BUFLEN OF SQL--CURRENT OF 
                                 SQL--HANDLES
                            IF SQL--STMTTYPE EQUAL SQL-DISCONNECT-ALL
                                 MOVE CS-FALSE TO SQL--THINKEXISTS OF
                                 SQL--CURRENTNAME OF SQL--HANDLES
                            ELSE
      * This may not be necessary, but we can never be too sure
                                 MOVE CS-TRUE TO SQL--THINKEXISTS OF
                                 SQL--CURRENTNAME OF SQL--HANDLES
                            END-IF
                            CALL "CSBOBJECTS" USING SQL--CTX OF 
                                 SQL--HANDLES SQL--RETCODE CS-CLEAR 
                                 SQL--CURRENTNAME OF
                                 SQL--HANDLES SQL--DUMMY SQL--DUMMY
                                 SQL--CURRENT OF SQL--HANDLES 
                                 SQL--LAST-NAME OF
                                 SQL--CONNNAME OF SQL--HANDLES
                       END-IF
      * Language statement types
                    WHEN SQL-MISC
                    WHEN SQL-TRANS
                    WHEN SQL-DELETE-SEARCHED
                    WHEN SQL-EXECUTE-PROCEDURE
                    WHEN SQL-INSERT-STMT
                    WHEN SQL-PREPARE-TRANS
                    WHEN SQL-SELECT-STMT
                    WHEN SQL-UPDATE-SEARCHED
                        PERFORM SQL--CMDOP
      * Cursor operations
                    WHEN SQL-OPEN-STMT
                    WHEN SQL-OPEN-WDESC-STMT 
                    WHEN SQL-DYNAMIC-DECLARE-CURSOR
                        MOVE SQL--CREATE TO SQL--ACTION
                        PERFORM SQL--CUROP
      * Result set operations
                    WHEN SQL-FETCH-STMT
                    WHEN SQL-FETCH-IDESC-STMT 
                    WHEN SQL-UPDATE-POSITIONED
                    WHEN SQL-DELETE-POSITIONED 
                        MOVE SQL--GET TO SQL--ACTION
                        PERFORM SQL--CUROP
                    WHEN SQL-CLOSE-STMT
                        MOVE SQL--GET TO SQL--ACTION
                        PERFORM SQL--CUROP
                        IF SQL--RETCODE EQUAL CS-SUCCEED
                            CALL "CTBCURSOR" USING SQL--COMMAND OF 
                                SQL--CONN OF SQL--HANDLES 
                                SQL--RETCODE 
                                CS-CURSOR-CLOSE
                                SQL--DUMMY CS-NULL-STRING SQL--DUMMY
                                CS-NULL-STRING CS-UNUSED
                        END-IF
                        IF SQL--RETCODE EQUAL CS-SUCCEED
                            CALL "CTBSEND" USING SQL--COMMAND OF 
                                SQL--CONN OF SQL--HANDLES SQL--RETCODE
                            PERFORM SQL--RESULTS
                        END-IF
                    WHEN SQL-DEALLOCATE-CURSOR-STMT
                        MOVE SQL--DESTROY TO SQL--ACTION
                        PERFORM SQL--CUROP
      * Dynamic statement operations
                    WHEN SQL-PREPARE
                        MOVE SQL--CREATE TO SQL--ACTION
                        PERFORM SQL--STMOP
                    WHEN SQL-DESCRIBE-IN
                    WHEN SQL-DESCRIBE-OUT
                    WHEN SQL-EXECUTE 
                        MOVE SQL--GET TO SQL--ACTION
                        PERFORM SQL--STMOP
                    WHEN SQL-DEALLOCATE-PREPARE
                        MOVE SQL--DESTROY TO SQL--ACTION
                        PERFORM SQL--STMOP
      * Dynamic descriptors
                    WHEN SQL-ALLOC-DESC 
                        MOVE SQL--CREATE TO SQL--ACTION
                        PERFORM SQL--DESCOP
                    WHEN SQL-SET-DESCRIPTOR
                    WHEN SQL-GET-DESCRIPTOR
                        MOVE SQL--GET TO SQL--ACTION
                        PERFORM SQL--DESCOP
                    WHEN SQL-DEALLOCATE-DESCRIPTOR
                        MOVE SQL--DESTROY TO SQL--ACTION
                        PERFORM SQL--DESCOP
      * Diagnostics
                    WHEN SQL-GET-DIAGNOSTICS
                    WHEN SQL-SET-DIAGNOSTIC-COUNT
      * Execute immediate uses default command handle, and can not be 
      * sticky
                    WHEN SQL-EXECUTE-IMMEDIATE 
                        CONTINUE
      * Exit statement
                    WHEN SQL-EXIT-STMT
                        CALL "CTBEXIT" USING 
                                SQL--CTX OF SQL--HANDLES
                                SQL--RETCODE
                                CS-FORCE-EXIT
                        IF (SQL--RETCODE EQUAL CS-SUCCEED)
                           CALL "CSBCTXDROP" USING
                                SQL--CTX OF SQL--HANDLES
                                SQL--RETCODE
                        END-IF          
      * Redundant, already handled
                    WHEN SQL-INIT-STMT
                    WHEN UNKNOWN-STMT
                        CONTINUE
                    WHEN OTHER  
      * Redundant, already handled
                        CONTINUE
                    END-EVALUATE
                END-IF
      * Call epilog if error
                IF SQL--RETCODE EQUAL CS-FAIL
                    MOVE SQL--RETCODE TO SQL--DUMMY
                    PERFORM SQL--EPILOG
                    MOVE SQL--DUMMY TO SQL--RETCODE
                END-IF
                IF SQL--RETCODE NOT EQUAL CS-FAIL
                        MOVE CS-SUCCEED TO SQL--RETCODE
                END-IF
      * Return immediately if failed in CTX or CTLib initialization 
            END-IF.
                
      ************************************************************
      * SQL--CURCNAME
      * Load current connection structure with connection name
      ************************************************************
        SQL--CURCNAME.
            MOVE CS-TRUE TO SQL--THINKEXISTS OF SQL--CURRENTNAME
                OF SQL--HANDLES
            MOVE SQL--NAME-SIZE TO SQL--BUFLEN OF SQL--CURRENT OF
                SQL--HANDLES
            CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                SQL--RETCODE CS-GET SQL--CURRENTNAME OF SQL--HANDLES
                SQL--DUMMY SQL--DUMMY
                SQL--CURRENT OF SQL--HANDLES
                SQL--LAST-NAME OF SQL--CONNNAME OF SQL--HANDLES
      *  the thinkexists element is initialized to FALSE, and is
      *  generally expected to be FALSE elsewhere, so set it back
            MOVE CS-FALSE TO SQL--THINKEXISTS OF SQL--CURRENTNAME
                OF SQL--HANDLES
            IF SQL--RETCODE EQUAL CS-SUCCEED
                MOVE SQL--BUFLEN OF SQL--CURRENT OF SQL--HANDLES
                    TO SQL--LNLEN OF SQL--CONNNAME OF SQL--HANDLES
            END-IF.
      ************************************************************
      * SQL--CONNOP
      *      Get/set information on connections as part of sqlprolog
      *      SQL--CONNOP is called with SQL--GET for all statement types 
      *      which require a connection including connects and disconnects.  
      *      It is called again if a connection needs to be created or 
      *      destroyed.
      *      However,   DISCONNECT ALL does not need to call SQL--CONNOP with
      *      SQL--GET to fetch the current connection, since no current 
      *      connection need exist.
      *      The DODECL flag is set to TRUE if a new connection is allocated
      *      , FALSE otherwise.
      * SQL--RETCODE = CS_FAIL if we are creating a connection which already 
      * exists.
      *         SQL--ACTION      defines operation SQL--GET, SQL--CREATE
      *                          or SQL--DESCTROY
      *         SQL--RETCODE     CS-SUCCEED or CS-FAIL
      ************************************************************
        SQL--CONNOP.
            MOVE CS-SUCCEED TO SQL--RETCODE
            MOVE CS-FALSE TO SQL--DODECL OF SQL--HANDLES
            IF SQL--ACTION EQUAL SQL--GET
                MOVE SQL--NULL TO SQL--COMMAND OF SQL--CONN OF
                    SQL--HANDLES
                MOVE SQL--NULL-CONNECTION TO SQL--CONNECTION OF 
                    SQL--CONN OF SQL--HANDLES
      * Try to get the current connection
                IF (SQL--LNLEN OF SQL--CONNNAME OF SQL--HANDLES 
                    EQUAL CS-UNUSED)
                    PERFORM SQL--CURCNAME
                END-IF
                MOVE CS-UNUSED TO SQL--BUFLEN OF SQL--CONN
                    OF SQL--HANDLES
                IF (SQL--RETCODE EQUAL CS-SUCCEED)
                    CALL "CSBOBJECTS" USING 
                        SQL--CTX OF SQL--HANDLES
                        SQL--RETCODE 
                        CS-GET
                        SQL--CONNNAME OF SQL--HANDLES
                        SQL--DUMMY SQL--DUMMY
                        SQL--CONN OF SQL--HANDLES
                        SQL--DUMMY
                END-IF
      * Clear out any old diagnostics on this connection
                IF ((SQL--RETCODE EQUAL CS-SUCCEED) AND
                    (SQL--ACTUALLYEXISTS OF SQL--CONN OF 
                    SQL--HANDLES EQUAL CS-TRUE) AND
                    (SQL--STMTTYPE NOT EQUAL 
                    SQL-GET-DIAGNOSTICS) AND
                    (SQL--STMTTYPE NOT EQUAL 
                    SQL-SET-DIAGNOSTIC-COUNT))
                    MOVE CS-CLEAR TO SQL--OPERATION
                    MOVE CS-UNUSED TO SQL--ERRINDEX
                    PERFORM SQL--CTBDIAG
                END-IF
            END-IF
      * End-if GET connection   

      * If SQL--CREATE and a connection exists
            IF ((SQL--RETCODE EQUAL CS-SUCCEED) AND
               (SQL--ACTION EQUAL SQL--CREATE)) 
                IF ( SQL--ACTUALLYEXISTS OF SQL--CONN OF 
                    SQL--HANDLES EQUAL CS-FALSE)
      * Connection (previously generated) 
                    MOVE SQL--NULL-CONNECTION TO SQL--CONNECTION
                         OF SQL--CONN OF SQL--HANDLES
                    MOVE SQL--NULL TO SQL--COMMAND
                         OF SQL--CONN OF SQL--HANDLES
                    CALL "CTBCONALLOC" USING SQL--CTX OF
                         SQL--HANDLES SQL--RETCODE SQL--CONNECTION
                         OF SQL--CONN OF SQL--HANDLES
                    IF SQL--RETCODE EQUAL CS-SUCCEED
                         MOVE CS-TRUE TO SQL--DODECL OF SQL--HANDLES
                         CALL "CTBDIAG" USING SQL--CONNECTION OF 
                         SQL--CONN OF SQL--HANDLES SQL--RETCODE 
                         CS-MF-WORD-COBOL CS-INIT CS-UNUSED 
                         CS-UNUSED SQL--DUMMY 
                    END-IF
                    IF SQL--RETCODE EQUAL CS-SUCCEED 
                         CALL "CTBCMDALLOC" USING SQL--CONNECTION 
                             OF SQL--CONN OF SQL--HANDLES 
                             SQL--RETCODE
                             SQL--COMMAND OF SQL--CONN OF 
                             SQL--HANDLES
                    ELSE                           
                         MOVE 25002 TO SQL--INTRERR 
                         PERFORM SQL--SETINTRERR
                    END-IF
                ELSE
      * Trying to connect to a connection that is in use
                    MOVE 25018 TO SQL--INTRERR
                    PERFORM SQL--SETINTRERR
                    MOVE CS-FAIL TO SQL--RETCODE
                END-IF
            END-IF
      * End-if Create connection        
      * Connection (generated) if SQL-DESTROY
            IF ((SQL--RETCODE EQUAL CS-SUCCEED) AND
               (SQL--ACTION EQUAL SQL--DESTROY)) 
                IF SQL--STMTTYPE OF SQL--HANDLES EQUAL 
                   SQL-DISCONNECT-ALL
      * Disconnect all - wildcards                 
      * Set up name descriptors
                   MOVE CS-WILDCARD TO SQL--LNLEN OF 
                       SQL--STMTNAME OF
                       SQL--HANDLES
                   MOVE CS-WILDCARD TO SQL--LNLEN OF SQL--CURNAME 
                       OF SQL--HANDLES
                ELSE
                   MOVE SQL--LNLEN OF SQL--CONNNAME OF 
                       SQL--HANDLES TO SQL--LNLEN OF 
                       SQL--STMTNAME OF SQL--HANDLES 
                   MOVE SQL--LAST-NAME OF SQL--CONNNAME OF 
                       SQL--HANDLES TO SQL--LAST-NAME OF 
                       SQL--STMTNAME OF SQL--HANDLES 
                   MOVE SQL--LNLEN OF SQL--CONNNAME 
                       OF SQL--HANDLES TO SQL--LNLEN OF 
                       SQL--CURNAME OF SQL--HANDLES 
                   MOVE SQL--LAST-NAME OF SQL--CONNNAME OF 
                       SQL--HANDLES TO SQL--LAST-NAME OF 
                       SQL--CURNAME OF SQL--HANDLES 
                END-IF
                MOVE CS-WILDCARD TO SQL--FNLEN OF 
                    SQL--STMTNAME OF SQL--HANDLES
                MOVE CS-FALSE TO SQL--THINKEXISTS OF 
                    SQL--STMTNAME OF SQL--HANDLES
                CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                    SQL--RETCODE CS-CLEAR SQL--STMTNAME OF
                    SQL--HANDLES SQL--DUMMY SQL--DUMMY 
                    SQL--STMT OF SQL--HANDLES SQL--DUMMY
                MOVE CS-WILDCARD TO SQL--FNLEN OF SQL--CURNAME OF
                    SQL--HANDLES
                MOVE CS-FALSE TO SQL--THINKEXISTS OF SQL--CURNAME 
                    OF SQL--HANDLES
                CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                    SQL--RETCODE CS-CLEAR SQL--CURNAME OF
                    SQL--HANDLES SQL--DUMMY SQL--DUMMY SQL--CUR
                    OF SQL--HANDLES SQL--DUMMY
      * Clear stmt-cursor table
                PERFORM SQL--CLEAR-STMT-CURS
      * Clear sticky stmt-command table
                PERFORM SQL--CLEAR-STMT-CMD
      * Drop connections
                IF SQL--STMTTYPE OF SQL--HANDLES EQUAL 
                    SQL-DISCONNECT-ALL
                    MOVE CS-FALSE TO SQL--THINKEXISTS OF 
                        SQL--CONNNAME OF SQL--HANDLES
                    MOVE CS-WILDCARD TO SQL--LNLEN OF SQL--CONNNAME OF
                        SQL--HANDLES
                    MOVE SQL--NAME-SIZE TO SQL--BUFLEN OF SQL--CONN OF
                        SQL--HANDLES
                    MOVE CS-UNUSED TO SQL--FNLEN OF SQL--CONNNAME OF
                        SQL--HANDLES
      * threadid ?
                    CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                        SQL--RETCODE CS-GET SQL--CONNNAME OF
                        SQL--HANDLES SQL--DUMMY SQL--DUMMY SQL--CONN
                        OF SQL--HANDLES SQL--LAST-NAME OF 
                        SQL--CONNNAME OF SQL--HANDLES
      * Loop through all connections
                    PERFORM UNTIL ((SQL--RETCODE NOT EQUAL CS-SUCCEED) 
                        OR
                        (SQL--ACTUALLYEXISTS OF SQL--CONN OF 
                        SQL--HANDLES NOT EQUAL CS-TRUE))
                        MOVE SQL--BUFLEN OF SQL--CONN OF
                             SQL--HANDLES TO SQL--LNLEN OF 
                             SQL--CONNNAME 
                             OF SQL--HANDLES
                        PERFORM SQL--DROPCONN

                        MOVE CS-WILDCARD TO SQL--LNLEN OF 
                            SQL--CONNNAME OF SQL--HANDLES
                        MOVE CS-FALSE TO SQL--THINKEXISTS OF 
                            SQL--CONNNAME OF SQL--HANDLES
                        MOVE SQL--NAME-SIZE TO SQL--BUFLEN OF 
                            SQL--CONN OF SQL--HANDLES
                        CALL "CSBOBJECTS" USING SQL--CTX OF 
                             SQL--HANDLES
                             SQL--RETCODE CS-GET SQL--CONNNAME OF
                             SQL--HANDLES SQL--DUMMY SQL--DUMMY 
                             SQL--CONN
                             OF SQL--HANDLES SQL--LAST-NAME OF 
                             SQL--CONNNAME OF SQL--HANDLES
                 END-PERFORM

      * Disconnect all connections
                ELSE
      * Disconnect single connection
                   PERFORM SQL--DROPCONN
                END-IF
      * End-if Destroy connection       
            END-IF.

      ************************************************************
      * SQL--DROPCONN
      ************************************************************
        SQL--DROPCONN.
            MOVE CS-SUCCEED TO SQL--RETCODE
            CALL "CTBCLOSE" USING SQL--CONNECTION OF
                 SQL--CONN OF SQL--HANDLES SQL--RETCODE
                 CS-UNUSED
            IF SQL--RETCODE NOT EQUAL CS-SUCCEED
                 CALL "CTBCLOSE" USING SQL--CONNECTION OF
                      SQL--CONN OF SQL--HANDLES SQL--RETCODE
                      CS-FORCE-CLOSE
            END-IF
            CALL "CTBCONDROP" USING SQL--CONNECTION OF
                 SQL--CONN OF SQL--HANDLES SQL--RETCODE
            MOVE SQL--NULL-CONNECTION TO SQL--CONNECTION OF
                 SQL--CONN OF SQL--HANDLES SQL--RETCODE
            MOVE CS-TRUE TO SQL--THINKEXISTS OF SQL--CONNNAME
                 OF SQL--HANDLES
            CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                 SQL--RETCODE CS-CLEAR SQL--CONNNAME OF
                 SQL--HANDLES SQL--DUMMY SQL--DUMMY SQL--CONN
                 OF SQL--HANDLES SQL--DUMMY.

      ************************************************************
      * SQL--INITSTMTCMD
      ************************************************************
        SQL--INITSTMTCMD.
      * Set first name is statement id and length  : 
            MOVE SQL--STMTIDLEN OF SQL--HANDLES TO SQL--FNLEN 
                OF SQL--STMT-CMD-NAME OF SQL--HANDLES
            MOVE SQL--STMTID OF SQL--HANDLES TO SQL--FIRST-NAME 
                OF SQL--STMT-CMD-NAME OF SQL--HANDLES
      * Set last name is connection name and length  : 
            MOVE SQL--LNLEN OF SQL--CONNNAME OF SQL--HANDLES
                TO SQL--LNLEN OF SQL--STMT-CMD-NAME OF SQL--HANDLES
            MOVE SQL--LAST-NAME OF SQL--CONNNAME OF SQL--HANDLES
                TO SQL--LAST-NAME OF SQL--STMT-CMD-NAME OF 
                SQL--HANDLES
            MOVE CS-FALSE TO SQL--THINKEXISTS OF SQL--STMT-CMD-NAME OF
                SQL--HANDLES
                
            MOVE SQL--STMTDATA-SIZE TO SQL--BUFLEN OF 
                    SQL--STMT-CMD OF SQL--HANDLES.

      ************************************************************
      * SQL--CMDOP
      *         Load and return command handle SQL--CONN OF SQL--HANDLES 
      * for a potentially sticky statement.  
      * Creates unique handle if persistent and does not exist for
      * the statement.  Initializes fields.
      * This function should not be called for other statements, unless
      * the Persistent field is certain to be set correctly.
      * Does not store the handle in cs_objects ->
      * SQL--EPILOG will do this if all goes well.
      * Return values:
      * SQL--RETCODE contains return code.
      * SQL--DODECL is CS-TRUE if handle was allocated.
      * SQL--CONN  OF SQL--HANDLES contains the statement command handle
      ************************************************************
        SQL--CMDOP.
            MOVE CS-SUCCEED TO SQL--RETCODE
            MOVE CS-FALSE TO SQL--DODECL OF SQL--HANDLES
            IF (SQL--PERSISTENT OF SQL--STMTDATA OF SQL--HANDLES
                EQUAL CS-TRUE)
                
                PERFORM SQL--INITSTMTCMD
      * Save the command handle as data
                CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                    SQL--RETCODE CS-GET
                    SQL--STMT-CMD-NAME OF SQL--HANDLES
                    SQL--DUMMY SQL--DUMMY
                    SQL--STMT-CMD OF SQL--HANDLES
                    SQL--STMTDATA OF SQL--HANDLES
                IF (SQL--RETCODE EQUAL CS-SUCCEED) AND
                        (SQL--ACTUALLYEXISTS OF SQL--STMT-CMD OF 
                        SQL--HANDLES EQUAL CS-TRUE)
                    MOVE SQL--COMMAND OF 
                        SQL--STMT-CMD OF SQL--HANDLES
                        TO SQL--COMMAND OF 
                        SQL--CONN OF SQL--HANDLES
                ELSE
      * allocate a command handle
                    CALL "CTBCMDALLOC" USING
                        SQL--CONNECTION OF SQL--CONN OF SQL--HANDLES 
                        SQL--RETCODE 
                        SQL--COMMAND OF SQL--CONN OF SQL--HANDLES
                    IF SQL--RETCODE EQUAL CS-SUCCEED
                        CALL "CTBCMDPROPS" USING
                            SQL--COMMAND OF SQL--CONN OF SQL--HANDLES
                            SQL--RETCODE CS-SET
                            CS-STICKY-BINDS
                            CS-TRUE
                            CS-UNUSED
                            SQL--NULL SQL--NULL
                        MOVE SQL--COMMAND OF SQL--CONN OF SQL--HANDLES
                            TO SQL--COMMAND OF 
                            SQL--STMT-CMD OF SQL--HANDLES
                        MOVE CS-TRUE TO SQL--PARAM OF SQL--STMTDATA OF 
                            SQL--HANDLES
                        MOVE CS-TRUE TO SQL--BIND OF SQL--STMTDATA OF 
                            SQL--HANDLES
                        MOVE CS-TRUE TO SQL--DODECL OF SQL--HANDLES
                    END-IF
                END-IF
            ELSE
      * Reset values for a default command handle
                MOVE CS-TRUE TO SQL--PARAM OF SQL--STMTDATA OF 
                    SQL--HANDLES
                MOVE CS-TRUE TO SQL--BIND OF SQL--STMTDATA OF 
                    SQL--HANDLES
            END-IF.

      ************************************************************
      * SQL--CUROP
      ************************************************************
        SQL--CUROP.
            MOVE CS-SUCCEED TO SQL--RETCODE 
            MOVE CS-FALSE TO SQL--DODECL OF SQL--HANDLES
            IF SQL--ACTION EQUAL SQL--CREATE
                MOVE CS-FALSE TO SQL--THINKEXISTS OF SQL--CURNAME 
                    OF SQL--HANDLES
                MOVE SQL--NULL TO SQL--COMMAND OF SQL--CUR OF
                    SQL--HANDLES
            ELSE
                MOVE CS-TRUE TO SQL--THINKEXISTS OF SQL--CURNAME 
                    OF SQL--HANDLES
            END-IF
      * For all operations, first perform a GET on the object
            MOVE SQL--CURDATA-SIZE TO SQL--BUFLEN OF SQL--CUR OF 
                SQL--HANDLES
            MOVE SQL--LNLEN OF SQL--CONNNAME OF SQL--HANDLES
                TO SQL--LNLEN OF SQL--CURNAME OF SQL--HANDLES
            MOVE SQL--LAST-NAME OF SQL--CONNNAME OF SQL--HANDLES
                TO SQL--LAST-NAME OF SQL--CURNAME OF SQL--HANDLES

            CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                SQL--RETCODE CS-GET SQL--CURNAME OF SQL--HANDLES
                SQL--DUMMY SQL--DUMMY SQL--CUR OF SQL--HANDLES
                SQL--STMTDATA OF SQL--HANDLES
             IF ((SQL--RETCODE EQUAL CS-SUCCEED) AND
                    (SQL--ACTION EQUAL SQL--CREATE) AND 
                (SQL--ACTUALLYEXISTS OF SQL--CUR OF SQL--HANDLES 
                NOT EQUAL CS-TRUE))
      * cursor does not exist, but that is OK. get a cmdhandle for it
                    MOVE CS-TRUE TO SQL--DODECL OF SQL--HANDLES
                    CALL "CTBCMDALLOC" USING
                        SQL--CONNECTION OF SQL--CONN OF 
                        SQL--HANDLES 
                        SQL--RETCODE 
                        SQL--COMMAND OF SQL--CUR OF SQL--HANDLES
                    IF SQL--RETCODE EQUAL CS-SUCCEED
                        MOVE SQL--CONNECTION OF SQL--CONN OF 
                            SQL--HANDLES
                            TO SQL--CONNECTION OF SQL--CUR OF 
                            SQL--HANDLES
                    END-IF
                    IF ((SQL--RETCODE EQUAL CS-SUCCEED) AND
                        (SQL--PERSISTENT OF SQL--STMTDATA OF 
                        SQL--HANDLES EQUAL CS-TRUE))
                        CALL "CTBCMDPROPS" USING
                            SQL--COMMAND OF SQL--CONN OF SQL--HANDLES
                            SQL--RETCODE CS-SET
                            CS-STICKY-BINDS
                            CS-TRUE
                            CS-UNUSED
                            SQL--NULL SQL--NULL
                    END-IF
                    MOVE CS-TRUE TO SQL--BIND OF SQL--STMTDATA OF
                        SQL--HANDLES
                    MOVE CS-TRUE TO SQL--PARAM OF SQL--STMTDATA OF
                        SQL--HANDLES
            END-IF
      * Assign command handle to generic SQL--COMMAND of SQL--CONN
            MOVE SQL--COMMAND OF SQL--CUR OF SQL--HANDLES TO
                SQL--COMMAND OF SQL--CONN OF SQL--HANDLES
            IF ((SQL--RETCODE EQUAL CS-SUCCEED) AND
               (SQL--STMTTYPE OF SQL--HANDLES EQUAL 
               SQL-DYNAMIC-DECLARE-CURSOR))

                CALL "CTBDYNAMIC" USING SQL--COMMAND OF SQL--CONN
                    OF SQL--HANDLES SQL--RETCODE
                    CS-CURSOR-DECLARE 
                    SQL--FIRST-NAME OF SQL--STMTNAME OF
                    SQL--HANDLES 
                    SQL--FNLEN OF SQL--STMTNAME OF 
                    SQL--HANDLES 
                    SQL--FIRST-NAME OF SQL--CURNAME OF
                    SQL--HANDLES 
                    SQL--FNLEN OF SQL--CURNAME OF 
                    SQL--HANDLES 
                IF SQL--RETCODE EQUAL CS-SUCCEED
                    CALL "CTBSEND" USING SQL--COMMAND OF SQL--CONN 
                        OF SQL--HANDLES SQL--RETCODE
                    PERFORM SQL--RESULTS
                END-IF
            END-IF

            IF ((SQL--RETCODE EQUAL CS-SUCCEED) AND
               (SQL--ACTION EQUAL SQL--DESTROY))
      * close + deallocate the cursor
                PERFORM SQL--CTDEALLOC-CURS
      * If this is a dynamic cursor, clear from the stmt-cursor table
                IF SQL--DYNSTMTLEN OF SQL--CURDATA OF SQL--HANDLES 
                    GREATER THAN 0
                    MOVE CS-CLEAR TO SQL--OPERATION
                    PERFORM SQL--DYNCUR
                END-IF
      * Drop cursor command handle
                CALL "CTBCMDDROP" USING
                    SQL--COMMAND OF SQL--CUR OF SQL--HANDLES
                    SQL--RETCODE
      * Clear cursor from cs_objects 
      * Set thinkexists to TRUE in case DYNCUR or CTDEALLOC-CURS
      * changed it
                MOVE CS-TRUE TO SQL--THINKEXISTS OF SQL--CURNAME
                    OF SQL--HANDLES
                CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                    SQL--RETCODE CS-CLEAR
                    SQL--CURNAME OF SQL--HANDLES
                    SQL--DUMMY SQL--DUMMY
                    SQL--CUR OF SQL--HANDLES
                    SQL--DUMMY
             END-IF.
      ************************************************************
      * SQL--STMOP
      ************************************************************
        SQL--STMOP.
            MOVE CS-SUCCEED TO SQL--RETCODE
            IF SQL--ACTION EQUAL SQL--CREATE
                MOVE CS-FALSE TO SQL--THINKEXISTS OF SQL--STMTNAME
                    OF SQL--HANDLES
                MOVE SQL--NULL TO SQL--COMMAND OF SQL--STMT OF
                    SQL--HANDLES
            ELSE
                MOVE CS-TRUE TO SQL--THINKEXISTS OF SQL--STMTNAME
                    OF SQL--HANDLES
            END-IF
      * GET the object
            MOVE SQL--STMTDATA-SIZE TO SQL--BUFLEN OF SQL--STMT 
                OF SQL--HANDLES
            MOVE SQL--LNLEN OF SQL--CONNNAME OF SQL--HANDLES
                TO SQL--LNLEN OF SQL--STMTNAME OF SQL--HANDLES
            MOVE SQL--LAST-NAME OF SQL--CONNNAME OF SQL--HANDLES
                TO SQL--LAST-NAME OF SQL--STMTNAME OF 
                SQL--HANDLES
            CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES 
                SQL--RETCODE CS-GET 
                SQL--STMTNAME OF SQL--HANDLES
                SQL--DUMMY SQL--DUMMY SQL--STMT OF SQL--HANDLES
                SQL--STMTDATA OF SQL--HANDLES
            IF ((SQL--RETCODE EQUAL CS-SUCCEED) AND
                (SQL--ACTUALLYEXISTS OF SQL--STMT OF SQL--HANDLES
                    EQUAL CS-TRUE) AND
                (SQL--ACTION EQUAL SQL--CREATE))
      * We are re-preparing a Statement. We must first close and
      *  deallocate any dynamic cursor which are open using this
      *  statement
                PERFORM SQL--DROP-STMT-CURS
      * Then we deallocate the statement and allocate/initialize a
      *  new one
                IF (SQL--RETCODE EQUAL CS-SUCCEED)
                    CALL "CTBDYNAMIC" USING 
                        SQL--COMMAND OF SQL--STMT OF SQL--HANDLES 
                        SQL--RETCODE
                        CS-DEALLOC
                        SQL--FIRST-NAME OF SQL--STMTNAME OF 
                                SQL--HANDLES
                        SQL--FNLEN OF SQL--STMTNAME OF SQL--HANDLES
                        SQL--DUMMY CS-NULL-STRING
                END-IF
                IF SQL--RETCODE EQUAL CS-SUCCEED
                    CALL "CTBSEND" USING SQL--COMMAND OF SQL--STMT 
                        OF SQL--HANDLES SQL--RETCODE
                    PERFORM UNTIL SQL--RETCODE NOT EQUAL CS-SUCCEED
                        CALL "CTBRESULTS" USING 
                            SQL--COMMAND OF SQL--STMT OF 
                            SQL--HANDLES 
                            SQL--RETCODE 
                            SQL--RESTYPE
                    END-PERFORM
                    IF SQL--RESTYPE EQUAL CS-CMD-DONE
                        MOVE CS-SUCCEED TO SQL--RETCODE
                    END-IF
                    IF SQL--RETCODE EQUAL CS-SUCCEED
                            CALL "CSBOBJECTS" USING 
                                SQL--CTX OF SQL--HANDLES
                                SQL--RETCODE CS-CLEAR
                                SQL--STMTNAME OF SQL--HANDLES
                                SQL--DUMMY SQL--DUMMY
                                SQL--STMT OF SQL--HANDLES
                                SQL--DUMMY
                    END-IF
                    MOVE CS-TRUE TO SQL--DODECL OF SQL--HANDLES
                END-IF
            ELSE 
                IF ((SQL--RETCODE EQUAL CS-SUCCEED) AND
                    (SQL--ACTION EQUAL SQL--CREATE)) 
      * We are preparing a Statement for the first time, allocate
      *  its command handle
                    CALL "CTBCMDALLOC" USING SQL--CONNECTION OF 
                        SQL--CONN OF SQL--HANDLES 
                        SQL--RETCODE 
                        SQL--COMMAND OF SQL--STMT OF SQL--HANDLES
                    IF SQL--RETCODE EQUAL CS-SUCCEED
                        MOVE CS-TRUE TO SQL--DODECL OF SQL--HANDLES 
                        MOVE SQL--CONNECTION OF SQL--CONN OF 
                            SQL--HANDLES TO SQL--CONNECTION OF 
                            SQL--STMT OF SQL--HANDLES 
                    END-IF
                ELSE
                    IF SQL--RETCODE EQUAL CS-SUCCEED
      * We are just getting a stmt
                        MOVE CS-FALSE TO SQL--DODECL OF SQL--HANDLES
                    END-IF
                    IF SQL--RETCODE EQUAL CS-HAFAILOVER
      * High Availability Failover has occurred.
                        MOVE 25019 TO SQL--INTRERR
                    END-IF
                END-IF
            END-IF
            MOVE SQL--COMMAND OF SQL--STMT OF SQL--HANDLES TO
                SQL--COMMAND OF SQL--CONN OF SQL--HANDLES
      * Destroy
            IF ((SQL--RETCODE EQUAL CS-SUCCEED) AND
                    (SQL--ACTION EQUAL SQL--DESTROY))
      * previously generated code
                PERFORM SQL--DROP-STMT-CURS
                CALL "CTBDYNAMIC" USING SQL--COMMAND OF SQL--CONN OF
                    SQL--HANDLES SQL--RETCODE CS-DEALLOC 
                    SQL--FIRST-NAME OF SQL--STMTNAME OF SQL--HANDLES
                    SQL--FNLEN OF SQL--STMTNAME OF SQL--HANDLES
                    SQL--DUMMY
                    CS-NULL-STRING
                IF SQL--RETCODE EQUAL CS-SUCCEED
                   CALL "CTBSEND" USING SQL--COMMAND OF SQL--CONN OF
                        SQL--HANDLES SQL--RETCODE
                   PERFORM SQL--RESULTS
                END-IF
                CALL "CTBCMDDROP" USING SQL--COMMAND OF SQL--CONN OF
                    SQL--HANDLES SQL--RETCODE
                MOVE CS-TRUE TO SQL--THINKEXISTS OF SQL--STMTNAME OF
                    SQL--HANDLES
                CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                    SQL--RETCODE CS-CLEAR SQL--STMTNAME OF
                    SQL--HANDLES SQL--DUMMY SQL--DUMMY SQL--STMT OF
                    SQL--HANDLES SQL--DUMMY
            END-IF.

      ************************************************************
      * SQL--CTDEALLOC-CURS
      * Deallocate a cursor
      * The variable SQL--HANDLES is the current esql handle
      * The variable SQL--COMMAND OF SQL--CONN SQL--HANDLES is the current 
      * command handle
      * The variable SQL--RETCODE will contain the return code
      ************************************************************
        SQL--CTDEALLOC-CURS.
            MOVE CS-SUCCEED TO SQL--RETCODE
      * obtain cursor information
            CALL "CTBCMDPROPS" USING
                        SQL--COMMAND OF SQL--CONN OF SQL--HANDLES
                        SQL--RETCODE CS-GET
                        CS-CUR-STATUS
                        SQL--CURSTATUS
                        CS-UNUSED
                        SQL--NULL SQL--NULL
            CALL "SQLMASKAND" USING
                        SQL--RETCODE SQL--CURSTATUS CS-CURSTAT-OPEN
            IF SQL--RETCODE GREATER THAN 0
      * close + deallocate the cursor
                CALL "CTBCURSOR" USING
                    SQL--COMMAND OF SQL--CONN OF SQL--HANDLES
                    SQL--RETCODE CS-CURSOR-CLOSE
                    SQL--NULL CS-NULL-STRING
                    SQL--NULL CS-NULL-STRING
                    CS-DEALLOC
            ELSE
      * the cursor was already closed, deallocate it
                CALL "CTBCURSOR" USING 
                    SQL--COMMAND OF SQL--CONN OF SQL--HANDLES 
                    SQL--RETCODE
                    CS-CURSOR-DEALLOC
                    SQL--NULL CS-NULL-STRING
                    SQL--NULL CS-NULL-STRING
                    CS-UNUSED
            END-IF
            IF SQL--RETCODE EQUAL CS-SUCCEED
                CALL "CTBSEND" USING
                    SQL--COMMAND OF SQL--CONN OF SQL--HANDLES
                    SQL--RETCODE
      * Assign command handle to 'utility' command handle variable
      * SQL--COMMAND OF SQL--CONN OF SQL--HANDLES for SQL--RESULTS
                PERFORM SQL--RESULTS
            END-IF.

      ************************************************************
      * SQL--RESULTS  
      * Purpose:
      * Generic results handling routine
      * The variable SQL--HANDLES is the current esql handle
      * The variable SQL--COMMAND OF SQL--CONN OF SQL--HANDLES is the 
      * current esql handle
      * The variable SQL--RETCODE will contain the return code
      * Temporary variables SQL--RESTYPE will contain the result type
      ************************************************************
        SQL--RESULTS.
            MOVE CS-SUCCEED TO SQL--RETCODE
            CALL "CTBRESULTS" USING SQL--COMMAND OF SQL--CONN OF
                        SQL--HANDLES SQL--RETCODE SQL--RESTYPE
            PERFORM UNTIL SQL--RETCODE NOT EQUAL CS-SUCCEED 
                   
                IF SQL--RESTYPE EQUAL CS-COMPUTE-RESULT 
                        MOVE 25003 TO SQL--INTRERR
                        PERFORM SQL--SETINTRERR
                        CALL "CTBCANCEL" USING SQL--NULL-CONNECTION
                            SQL--RETCODE SQL--COMMAND OF SQL--CONN OF
                            SQL--HANDLES CS-CANCEL-CURRENT
                END-IF
                IF SQL--RESTYPE EQUAL CS-CURSOR-RESULT 
                        MOVE 25004 TO SQL--INTRERR
                        PERFORM SQL--SETINTRERR
                        CALL "CTBCANCEL" USING SQL--NULL-CONNECTION
                            SQL--RETCODE SQL--COMMAND OF SQL--CONN OF
                            SQL--HANDLES CS-CANCEL-CURRENT
                END-IF
                IF SQL--RESTYPE EQUAL CS-PARAM-RESULT
                        MOVE 25005 TO SQL--INTRERR
                        PERFORM SQL--SETINTRERR
                        CALL "CTBCANCEL" USING SQL--NULL-CONNECTION
                            SQL--RETCODE SQL--COMMAND OF SQL--CONN OF
                            SQL--HANDLES CS-CANCEL-CURRENT
                END-IF
                IF SQL--RESTYPE EQUAL CS-ROW-RESULT 
                        MOVE 25006 TO SQL--INTRERR
                        PERFORM SQL--SETINTRERR
                        CALL "CTBCANCEL" USING SQL--NULL-CONNECTION
                            SQL--RETCODE SQL--COMMAND OF SQL--CONN OF
                            SQL--HANDLES CS-CANCEL-CURRENT
                END-IF
                IF SQL--RESTYPE EQUAL CS-STATUS-RESULT 
                        MOVE 25009 TO SQL--INTRERR
                        PERFORM SQL--SETINTRERR
                        CALL "CTBCANCEL" USING SQL--NULL-CONNECTION
                            SQL--RETCODE SQL--COMMAND OF SQL--CONN OF
                            SQL--HANDLES CS-CANCEL-CURRENT
                END-IF
                IF SQL--RESTYPE EQUAL CS-DESCRIBE-RESULT 
                        MOVE 25010 TO SQL--INTRERR
                        PERFORM SQL--SETINTRERR
                END-IF
                   
                CALL "CTBRESULTS" USING SQL--COMMAND OF SQL--CONN OF
                        SQL--HANDLES SQL--RETCODE SQL--RESTYPE
                END-PERFORM
                IF SQL--RETCODE EQUAL CS-HAFAILOVER
                        MOVE 25019 TO SQL--INTRERR
                END-IF
                IF SQL--RETCODE NOT EQUAL CS-END-RESULTS AND
                    SQL--RETCODE NOT EQUAL CS-CANCELED 
                        CALL "CTBCANCEL" USING SQL--NULL-CONNECTION
                            SQL--RETCODE SQL--COMMAND OF SQL--CONN OF
                            SQL--HANDLES CS-CANCEL-ALL
                ELSE
                   IF SQL--RETCODE NOT EQUAL CS-CANCELED
                        MOVE CS-SUCCEED TO SQL--RETCODE
		   END-IF
                END-IF.
      ************************************************************
      * SQL--CLEAR-STMT-CMD
      * Clear all statement command handles from cs-objects
      * for one or all connections
      ************************************************************

        SQL--CLEAR-STMT-CMD.
            MOVE CS-SUCCEED TO SQL--RETCODE
            MOVE CS-WILDCARD TO SQL--FNLEN OF SQL--STMT-CMD-NAME OF
                SQL--HANDLES
            IF SQL--STMTTYPE OF SQL--HANDLES EQUAL 
                SQL-DISCONNECT-ALL
      * Last name is Wildcard
                MOVE CS-WILDCARD TO SQL--LNLEN OF SQL--STMT-CMD-NAME OF
                    SQL--HANDLES
            ELSE
                MOVE SQL--LAST-NAME OF SQL--CONNNAME OF 
                    SQL--HANDLES TO SQL--LAST-NAME OF 
                    SQL--STMT-CMD-NAME OF SQL--HANDLES
                MOVE SQL--LNLEN OF SQL--CONNNAME OF 
                    SQL--HANDLES TO SQL--LNLEN OF 
                    SQL--STMT-CMD-NAME OF SQL--HANDLES
            END-IF
      * Thinkexists is FALSE in case there are no objects
            MOVE CS-FALSE TO SQL--THINKEXISTS OF
                SQL--STMT-CMD-NAME OF SQL--HANDLES
            CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                SQL--RETCODE CS-CLEAR
                SQL--STMT-CMD-NAME OF SQL--HANDLES
                SQL--DUMMY SQL--DUMMY
                SQL--STMT-CMD OF SQL--HANDLES
                SQL--DUMMY.

      ************************************************************
      * SQL--CLEAR-STMT-CURS
      * Clear all statement-cursors relations from cs-objects
      * for one or all connections, if any (thinkexists = CS-FALSE)
      ************************************************************
        SQL--CLEAR-STMT-CURS.
            MOVE CS-WILDCARD TO SQL--FNLEN OF SQL--STMT-CUR-NAME OF
                SQL--HANDLES
            MOVE CS-WILDCARD TO SQL--LNLEN OF SQL--STMT-CUR-NAME OF
                SQL--HANDLES
            MOVE CS-FALSE TO SQL--THINKEXISTS OF
                SQL--STMT-CUR-NAME OF SQL--HANDLES
            IF SQL--STMTTYPE OF SQL--HANDLES EQUAL 
                SQL-DISCONNECT-ALL
                MOVE CS-WILDCARD TO SQL--SCOPELEN OF 
                    SQL--STMT-CUR-NAME OF SQL--HANDLES
            ELSE
                MOVE SQL--POINTER-SIZE TO SQL--SCOPELEN OF 
                    SQL--STMT-CUR-NAME OF SQL--HANDLES
            END-IF
            CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                SQL--RETCODE CS-CLEAR
                SQL--STMT-CUR-NAME OF SQL--HANDLES
                SQL--CONNECTION OF SQL--CONN OF SQL--HANDLES 
                SQL--DUMMY
                SQL--STMT-CUR OF SQL--HANDLES
                SQL--DUMMY.

      ************************************************************
      * SQL--DROP-STMT-CURS
      * Drop all cursors associated with a Dynamic statement
      ************************************************************
        SQL--DROP-STMT-CURS.
            MOVE CS-SUCCEED TO SQL--RETCODE
            MOVE SQL--FNLEN OF SQL--STMTNAME OF SQL--HANDLES
                TO SQL--LNLEN OF SQL--STMT-CUR-NAME OF 
                SQL--HANDLES
            MOVE SQL--FIRST-NAME OF SQL--STMTNAME OF SQL--HANDLES
                TO SQL--LAST-NAME OF SQL--STMT-CUR-NAME OF 
                SQL--HANDLES
            MOVE CS-WILDCARD TO SQL--FNLEN OF SQL--STMT-CUR-NAME OF
                SQL--HANDLES
            MOVE SQL--NAME-SIZE TO SQL--BUFLEN OF SQL--STMT-CUR OF 
                SQL--HANDLES
            MOVE SQL--LNLEN OF SQL--CONNNAME OF SQL--HANDLES
                TO SQL--LNLEN OF SQL--CURNAME OF SQL--HANDLES
            MOVE SQL--LAST-NAME OF SQL--CONNNAME OF SQL--HANDLES
                TO SQL--LAST-NAME OF SQL--CURNAME OF SQL--HANDLES
      * Add new key (scope=connection handle,scopelen=sizeof)
            MOVE SQL--POINTER-SIZE TO SQL--SCOPELEN OF 
                SQL--STMT-CUR-NAME OF SQL--HANDLES

      * There aren't necessarily any cursors left
            MOVE CS-FALSE TO SQL--THINKEXISTS OF
                    SQL--STMT-CUR-NAME OF SQL--HANDLES
            CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                SQL--RETCODE CS-GET
                SQL--STMT-CUR-NAME OF SQL--HANDLES
                SQL--CONNECTION OF SQL--CONN OF SQL--HANDLES 
                SQL--DUMMY 
                SQL--STMT-CUR OF SQL--HANDLES
                SQL--CMDTEXT
      * Loop over all cursors on this statement
            PERFORM UNTIL ((SQL--RETCODE NOT EQUAL CS-SUCCEED) OR
                (SQL--ACTUALLYEXISTS OF SQL--STMT-CUR OF SQL--HANDLES
                NOT EQUAL CS-TRUE))
                MOVE SQL--BUFLEN OF SQL--STMT-CUR OF SQL--HANDLES
                    TO SQL--FNLEN OF SQL--CURNAME OF SQL--HANDLES
                MOVE SQL--CMDTEXT TO 
                    SQL--FIRST-NAME OF SQL--CURNAME OF SQL--HANDLES
                MOVE CS-UNUSED TO 
                        SQL--BUFLEN OF SQL--CUR OF SQL--HANDLES
                CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                    SQL--RETCODE CS-GET
                    SQL--CURNAME OF SQL--HANDLES
                    SQL--DUMMY SQL--DUMMY
                    SQL--CUR OF SQL--HANDLES
                    SQL--DUMMY
                MOVE SQL--CURDATA-SIZE TO SQL--BUFLEN OF SQL--CUR 
                    OF SQL--HANDLES
                IF ((SQL--RETCODE EQUAL CS-SUCCEED) AND
                    (SQL--ACTUALLYEXISTS OF SQL--CUR OF SQL--HANDLES
                    EQUAL CS-TRUE))
      * close + deallocate the cursor
                    MOVE SQL--COMMAND OF SQL--CUR OF SQL--HANDLES TO
                        SQL--COMMAND OF SQL--CONN OF SQL--HANDLES 
                    PERFORM SQL--CTDEALLOC-CURS
                    IF SQL--RETCODE EQUAL CS-SUCCEED
                        CALL "CTBCMDDROP" USING
                            SQL--COMMAND OF SQL--CUR OF SQL--HANDLES
                            SQL--RETCODE
                    END-IF
      * Restore stmt command handle 
                    MOVE SQL--COMMAND OF SQL--STMT OF SQL--HANDLES TO
                        SQL--COMMAND OF SQL--CONN OF SQL--HANDLES 
                END-IF
      * Clear any reference to this cursor from cs_objects date
                CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                    SQL--RETCODE CS-CLEAR
                    SQL--CURNAME OF SQL--HANDLES
                    SQL--DUMMY SQL--DUMMY
                    SQL--CUR OF SQL--HANDLES
                    SQL--DUMMY
                MOVE SQL--FNLEN OF SQL--CURNAME OF SQL--HANDLES
                    TO SQL--FNLEN OF SQL--STMT-CUR-NAME OF SQL--HANDLES
                MOVE SQL--FIRST-NAME OF SQL--CURNAME OF SQL--HANDLES
                    TO SQL--FIRST-NAME OF SQL--STMT-CUR-NAME OF
                        SQL--HANDLES
                CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                    SQL--RETCODE CS-CLEAR
                    SQL--STMT-CUR-NAME OF SQL--HANDLES
                    SQL--CONNECTION OF SQL--CONN OF SQL--HANDLES
                    SQL--DUMMY
                    SQL--STMT-CUR OF SQL--HANDLES
                    SQL--DUMMY
      * Get the next cursor
                MOVE CS-WILDCARD TO SQL--FNLEN OF SQL--STMT-CUR-NAME OF
                    SQL--HANDLES
                MOVE SQL--NAME-SIZE TO SQL--BUFLEN OF SQL--STMT-CUR OF 
                    SQL--HANDLES
                IF SQL--RETCODE EQUAL CS-SUCCEED
                    CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                            SQL--RETCODE CS-GET
                            SQL--STMT-CUR-NAME OF SQL--HANDLES
                            SQL--DUMMY SQL--DUMMY
                            SQL--STMT-CUR OF SQL--HANDLES
                            SQL--CMDTEXT
      * Previous CLEAR operation may have removed the last dynstmt/
      * cursor relation, in which case this GET operation failed, 
      * this is OK
                    MOVE CS-SUCCEED TO SQL--RETCODE
                END-IF
            END-PERFORM.

      ************************************************************
      * SQL--DYNCUR.
      * Make an association between a Dynamic stmt and a cursor.
      * The variable SQL--HANDLES is the current esql handle
      * The variable SQL--OPERATION is used to determine 
      * The variable SQL--RETCODE will contain the return code
      ************************************************************
        SQL--DYNCUR.
            MOVE CS-SUCCEED TO SQL--RETCODE

      * Handle stmt-cursor relation
      * Dynamic Statement name to cur-name last name (instead of
      * whatever is in SQL--STMTNAME)
            MOVE SQL--DYNSTMTLEN OF SQL--CURDATA OF SQL--HANDLES
                TO SQL--LNLEN OF SQL--STMT-CUR-NAME OF 
                SQL--HANDLES
            MOVE SQL--DYNSTMTNAME OF SQL--CURDATA OF SQL--HANDLES
                TO SQL--LAST-NAME OF SQL--STMT-CUR-NAME OF 
                SQL--HANDLES
      * Cursor first name to cur-name first name
            MOVE SQL--FNLEN OF SQL--CURNAME OF SQL--HANDLES
                TO SQL--FNLEN OF SQL--STMT-CUR-NAME OF SQL--HANDLES
            MOVE SQL--FIRST-NAME OF SQL--CURNAME OF SQL--HANDLES
                TO SQL--FIRST-NAME OF SQL--STMT-CUR-NAME OF 
                SQL--HANDLES
      * Add new key (scope=connection handle,scopelen=sizeof)
            MOVE SQL--POINTER-SIZE TO SQL--SCOPELEN OF 
                SQL--STMT-CUR-NAME OF SQL--HANDLES

      * If we are getting or clearing,  BUFLEN is different         
            IF SQL--OPERATION EQUAL CS-SET
                MOVE SQL--FNLEN OF SQL--CURNAME OF SQL--HANDLES TO
                    SQL--BUFLEN OF SQL--STMT-CUR OF SQL--HANDLES
                MOVE CS-FALSE TO SQL--THINKEXISTS OF
                    SQL--STMT-CUR-NAME OF SQL--HANDLES
            ELSE
                MOVE SQL--NAME-SIZE TO SQL--BUFLEN OF 
                     SQL--STMT-CUR OF SQL--HANDLES
                MOVE CS-TRUE TO SQL--THINKEXISTS OF
                     SQL--STMT-CUR-NAME OF SQL--HANDLES
            END-IF
            CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                 SQL--RETCODE SQL--OPERATION
                 SQL--STMT-CUR-NAME OF SQL--HANDLES
                 SQL--CONNECTION OF SQL--CONN OF SQL--HANDLES 
                 SQL--DUMMY
                 SQL--STMT-CUR OF SQL--HANDLES
                 SQL--FIRST-NAME OF SQL--CURNAME OF SQL--HANDLES.

      ************************************************************
      * SQL--DESCOP
      * Get/Set info on a Dynamic Descriptor as part of SQL--PROLOG
      ************************************************************
        SQL--DESCOP.
            MOVE CS-SUCCEED TO SQL--RETCODE
            IF SQL--ACTION EQUAL SQL--CREATE
      * SQL--CREATE : Try to create a descriptor
                MOVE CS-FALSE TO SQL--THINKEXISTS OF SQL--DESCNAME OF
                    SQL--HANDLES
      * Try to store object now before going further to see if it already 
      * exists- if errors, we will not continue, epilog will clean up
                IF SQL--RETCODE EQUAL CS-SUCCEED
                    CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                        SQL--RETCODE CS-SET
                        SQL--DESCNAME OF SQL--HANDLES
                        SQL--DUMMY SQL--DUMMY
                        SQL--DESC OF SQL--HANDLES
                        SQL--DUMMY
                END-IF
                IF (SQL--RETCODE EQUAL CS-SUCCEED) 
                    CALL "CTB2DYNDESC" USING
                        SQL--COMMAND OF SQL--DESC OF SQL--HANDLES
                        SQL--RETCODE
                        SQL--FIRST-NAME OF SQL--DESCNAME OF 
                                SQL--HANDLES
                        SQL--FNLEN OF SQL--DESCNAME OF SQL--HANDLES
                        CS-TRUE
                        CS-ALLOC
                        SQL--DESCSIZE OF SQL--HANDLES
                        SQL--DFMTUTIL SQL--DUMMY CS-PARAM-NULL
                        SQL--DUMMY CS-PARAM-NULL
                        SQL--DUMMY CS-PARAM-NULL SQL--DUMMY
      *  This fix will depend  on changes in ct-lib- and may be performed
      *  elsewhere in the code (SQL--DESCCONN).
      *  Currently a descriptor goes away WHEN you drop the connection
      *  It is allocated from.  Until this is fixed in ct-lib, keep the
      *  connection and command handles until the descriptor is dropped
      *             CALL "CTBCMDDROP" USING
      *                 SQL--COMMAND OF SQL--DESC OF SQL--HANDLES
      *                 SQL--RETCODE
      *             CALL "CTBCONDROP" USING
      *                 SQL--CONNECTION OF SQL--DESC OF SQL--HANDLES
      *                 SQL--RETCODE
                END-IF
            END-IF
      * SQL--GET or SQL--DESTROY: get the object first
            IF (SQL--RETCODE EQUAL CS-SUCCEED AND
               ((SQL--ACTION EQUAL SQL--GET) OR
               (SQL--ACTION EQUAL SQL--DESTROY)) )
                MOVE CS-TRUE TO SQL--THINKEXISTS OF SQL--DESCNAME OF
                    SQL--HANDLES
      * Get the object first
                CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                    SQL--RETCODE CS-GET
                    SQL--DESCNAME OF SQL--HANDLES
                    SQL--DUMMY SQL--DUMMY
                    SQL--DESC OF SQL--HANDLES
                    SQL--DUMMY
            END-IF

      * Destroy
            IF (SQL--RETCODE EQUAL CS-SUCCEED) AND
               (SQL--ACTION EQUAL SQL--DESTROY)
               CALL "CTB2DYNDESC" USING SQL--COMMAND OF 
                   SQL--DESC OF
                   SQL--HANDLES SQL--RETCODE SQL--FIRST-NAME OF
                   SQL--DESCNAME OF SQL--HANDLES SQL--FNLEN OF
                   SQL--DESCNAME OF SQL--HANDLES CS-TRUE CS-DEALLOC
                   CS-UNUSED SQL--DUMMY SQL--DUMMY CS-PARAM-NULL
                   SQL--DUMMY CS-PARAM-NULL SQL--DUMMY 
                   CS-PARAM-NULL
                   SQL--DUMMY
      * Neither the connection nor command handle is dropped
      * not necessary
               MOVE CS-TRUE TO SQL--THINKEXISTS OF SQL--DESCNAME OF
                    SQL--HANDLES
               CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                    SQL--RETCODE CS-CLEAR SQL--DESCNAME OF
                    SQL--HANDLES SQL--DUMMY SQL--DUMMY 
                    SQL--DESC OF
                    SQL--HANDLES SQL--DUMMY
            END-IF.

      ************************************************************
      * SQL--DESCCONN
      * Allocate or retrieve special connection and command handles
      * for dynamic descriptors.
      ************************************************************
        SQL--DESCCONN.
            MOVE CS-SUCCEED TO SQL--RETCODE
            MOVE CS-FALSE TO SQL--DODECL OF SQL--HANDLES
            MOVE SQL--NULL-CONNECTION TO SQL--CONNECTION OF 
                SQL--CONN OF SQL--HANDLES
            MOVE SQL--NULL TO SQL--COMMAND OF 
                SQL--CONN OF SQL--HANDLES 
            CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                SQL--RETCODE CS-GET
                SQL--DESC-CONN-NAME OF SQL--HANDLES
                SQL--DUMMY SQL--DUMMY
                SQL--DESC-CONN OF SQL--HANDLES
                SQL--DUMMY.
            IF (SQL--RETCODE EQUAL CS-SUCCEED) AND
                (SQL--ACTUALLYEXISTS OF SQL--DESC-CONN OF 
                    SQL--HANDLES EQUAL CS-TRUE)
                    MOVE SQL--CONNECTION OF 
                        SQL--DESC-CONN OF SQL--HANDLES TO
                        SQL--CONNECTION OF SQL--DESC OF 
                        SQL--HANDLES
                    MOVE SQL--COMMAND OF 
                        SQL--DESC-CONN OF SQL--HANDLES TO
                        SQL--COMMAND OF SQL--DESC OF 
                        SQL--HANDLES
                    MOVE SQL--CONNECTION OF SQL--DESC OF 
                        SQL--HANDLES TO
                        SQL--CONNECTION OF SQL--CONN OF 
                        SQL--HANDLES
      * Clear previous diagnostics 
                    MOVE CS-CLEAR TO SQL--OPERATION
                    MOVE CS-UNUSED TO SQL--ERRINDEX
                    PERFORM SQL--CTBDIAG
            ELSE
                CALL "CTBCONALLOC" USING
                    SQL--CTX OF SQL--HANDLES
                    SQL--RETCODE
                    SQL--CONNECTION OF SQL--DESC OF SQL--HANDLES
                IF (SQL--RETCODE EQUAL CS-SUCCEED) 
                    MOVE SQL--CONNECTION OF SQL--DESC OF 
                        SQL--HANDLES TO SQL--CONNECTION OF 
                        SQL--CONN OF SQL--HANDLES 
      * Init diagnostics 
                    MOVE CS-CLEAR TO SQL--OPERATION
                    MOVE CS-UNUSED TO SQL--ERRINDEX
                    PERFORM SQL--CTBDIAG
                END-IF
      * allocate a new command handle
                IF (SQL--RETCODE EQUAL CS-SUCCEED) 
                    CALL "CTBCMDALLOC" USING
                        SQL--CONNECTION OF SQL--DESC OF SQL--HANDLES
                        SQL--RETCODE
                        SQL--COMMAND OF SQL--DESC OF SQL--HANDLES
                END-IF
                IF (SQL--RETCODE EQUAL CS-SUCCEED) 
                    MOVE SQL--CONNECTION OF SQL--DESC OF 
                        SQL--HANDLES TO SQL--CONNECTION OF 
                        SQL--DESC-CONN OF SQL--HANDLES 
                    MOVE SQL--COMMAND OF SQL--DESC OF 
                        SQL--HANDLES TO SQL--COMMAND OF 
                        SQL--DESC-CONN OF SQL--HANDLES 
                    CALL "CSBOBJECTS" USING SQL--CTX OF SQL--HANDLES
                        SQL--RETCODE CS-SET
                        SQL--DESC-CONN-NAME OF SQL--HANDLES
                        SQL--DUMMY SQL--DUMMY
                        SQL--DESC-CONN OF SQL--HANDLES
                        SQL--DUMMY
                ELSE
      * If new connection  but no command handle, set DODECL and let
      * epilog handle it.
                    MOVE CS-TRUE TO SQL--DODECL OF SQL--HANDLES
                END-IF
            END-IF
      * If everything went ok
            IF (SQL--RETCODE EQUAL CS-SUCCEED) 
                MOVE SQL--COMMAND OF SQL--DESC OF SQL--HANDLES 
                    TO SQL--COMMAND OF SQL--CONN OF SQL--HANDLES 
            END-IF.


      ************************************************************
      * SQL--EPILOG  
      ************************************************************
        SQL--EPILOG.

            MOVE CS-FALSE TO SQL--ERRORS
            MOVE CS-SUCCEED TO SQL--RETCODE
            MOVE 0 TO SQL--WORST-INDEX
            IF
             (SQL--STMTTYPE OF SQL--HANDLES  LESS THAN UNKNOWN-STMT) 
             OR
             (SQL--STMTTYPE OF SQL--HANDLES  EQUAL UNKNOWN-STMT) 
             OR
             (SQL--STMTTYPE OF SQL--HANDLES  GREATER THAN 
             MAX-SQL-STMT) 
                    MOVE 25002 TO  SQL--INTRERR
                    PERFORM SQL--SETINTRERR
                    MOVE CS-FAIL TO SQL--RETCODE 
            END-IF
      * Look only for the types which need not operate on connections
      * All others require connections, including dynamic descriptors


            EVALUATE SQL--STMTTYPE OF SQL--HANDLES 

      * Diagnostics
                WHEN SQL-GET-DIAGNOSTICS
                WHEN SQL-SET-DIAGNOSTIC-COUNT
                WHEN SQL-INIT-STMT
                WHEN SQL-EXIT-STMT
                    MOVE CS-FALSE TO SQL--HAVE-CONN
                WHEN OTHER
                    IF (SQL--CONNECTION OF SQL--CONN OF 
                        SQL--HANDLES NOT EQUAL  
                        SQL--NULL-CONNECTION) 
                        MOVE CS-TRUE TO SQL--HAVE-CONN
                    ELSE
                        MOVE CS-FALSE TO SQL--HAVE-CONN
                    END-IF
            END-EVALUATE

            MOVE CS-FALSE TO SQL--ERRORS 
            IF (SQL--RETCODE EQUAL CS-SUCCEED)
                 PERFORM SQL--WORST
            ELSE
                 MOVE CS-TRUE TO SQL--ERRORS 
            END-IF
      * Handle specific statement types
            EVALUATE SQL--STMTTYPE OF SQL--HANDLES 
                WHEN SQL-ANSI-CONNECT
                WHEN SQL-NONANSI-CONNECT
      * If errors on CONNECT , handle error messages first then drop
      * connection  later.
                   IF (SQL--ERRORS EQUAL CS-FALSE)
      * Set connection data and current connection name
                       MOVE CS-FALSE TO SQL--THINKEXISTS OF
                           SQL--CONNNAME OF SQL--HANDLES
                       MOVE SQL--LNLEN OF SQL--CONNNAME OF 
                           SQL--HANDLES TO SQL--BUFLEN OF 
                           SQL--CONN OF SQL--HANDLES
                       CALL "CSBOBJECTS" USING SQL--CTX OF
                           SQL--HANDLES SQL--RETCODE CS-SET
                           SQL--CONNNAME OF SQL--HANDLES 
                           SQL--DUMMY
                           SQL--DUMMY SQL--CONN OF SQL--HANDLES
                           SQL--LAST-NAME OF SQL--CONNNAME OF
                           SQL--HANDLES
                       IF SQL--RETCODE EQUAL CS-SUCCEED
                           MOVE CS-FALSE TO SQL--THINKEXISTS OF
                               SQL--CURRENTNAME OF SQL--HANDLES
                           MOVE SQL--LNLEN OF SQL--CONNNAME OF
                               SQL--HANDLES TO SQL--BUFLEN OF
                               SQL--CURRENT OF SQL--HANDLES
                           CALL "CSBOBJECTS" USING SQL--CTX OF
                               SQL--HANDLES SQL--RETCODE CS-CLEAR
                               SQL--CURRENTNAME OF SQL--HANDLES
                               SQL--DUMMY SQL--DUMMY 
                               SQL--CURRENT OF
                               SQL--HANDLES SQL--LAST-NAME OF
                               SQL--CONNNAME OF SQL--HANDLES
                       END-IF
                       IF SQL--RETCODE EQUAL CS-SUCCEED
                           CALL "CSBOBJECTS" USING SQL--CTX OF
                               SQL--HANDLES SQL--RETCODE CS-SET
                               SQL--CURRENTNAME OF SQL--HANDLES
                               SQL--DUMMY SQL--DUMMY 
                               SQL--CURRENT OF
                               SQL--HANDLES SQL--LAST-NAME OF
                               SQL--CONNNAME OF SQL--HANDLES
                       END-IF
                    END-IF

      * Language statement types
                WHEN SQL-MISC
                WHEN SQL-TRANS
                WHEN SQL-DELETE-SEARCHED
                WHEN SQL-EXECUTE-PROCEDURE
                WHEN SQL-INSERT-STMT
                WHEN SQL-PREPARE-TRANS
                WHEN SQL-SELECT-STMT
                WHEN SQL-UPDATE-SEARCHED
      * Drop sticky command handle
                    IF (SQL--ERRORS EQUAL CS-TRUE) AND
                        (SQL--DODECL OF SQL--HANDLES EQUAL
                        CS-TRUE) AND
                        (SQL--COMMAND OF SQL--CONN  OF
                        SQL--HANDLES NOT EQUAL SQL--NULL)
                        CALL "CTBCMDDROP" USING
                            SQL--COMMAND OF SQL--CONN OF 
                            SQL--HANDLES
                            SQL--RETCODE
                        MOVE SQL--NULL TO SQL--COMMAND OF 
                            SQL--CONN OF SQL--HANDLES
                    END-IF
      * If no errors
                    IF ((SQL--ERRORS EQUAL CS-FALSE) AND
                        ((SQL--DODECL OF SQL--HANDLES EQUAL
                        CS-TRUE) OR  (
                        (SQL--PERSISTENT OF SQL--STMTDATA OF
                        SQL--HANDLES EQUAL CS-TRUE) AND
                        ((SQL--PARAM OF SQL--STMTDATA OF
                        SQL--HANDLES EQUAL CS-TRUE) OR
                        (SQL--BIND OF SQL--STMTDATA OF
                        SQL--HANDLES EQUAL CS-TRUE)) ) ) ) 
                            PERFORM SQL--INITSTMTCMD
                            MOVE CS-FALSE TO SQL--PARAM OF 
                                SQL--STMTDATA OF SQL--HANDLES 
                            MOVE CS-FALSE TO SQL--BIND OF 
                                SQL--STMTDATA OF SQL--HANDLES 
                            IF (SQL--DODECL OF SQL--HANDLES EQUAL
                                CS-TRUE) 
                                MOVE CS-FALSE TO SQL--THINKEXISTS 
                                    OF
                                    SQL--STMT-CMD-NAME OF 
                                    SQL--HANDLES
                            ELSE
                                MOVE CS-TRUE TO SQL--THINKEXISTS 
                                    OF
                                    SQL--STMT-CMD-NAME OF 
                                    SQL--HANDLES
                            END-IF
                            MOVE SQL--COMMAND OF SQL--CONN OF
                                SQL--HANDLES TO SQL--COMMAND OF
                                SQL--STMT-CMD OF SQL--HANDLES
                            CALL "CSBOBJECTS" USING SQL--CTX OF 
                                SQL--HANDLES SQL--RETCODE CS-SET
                                SQL--STMT-CMD-NAME OF SQL--HANDLES
                                SQL--DUMMY SQL--DUMMY
                                SQL--STMT-CMD OF SQL--HANDLES
                                SQL--STMTDATA OF SQL--HANDLES
                    END-IF

      * Cursor operations
                WHEN SQL-DYNAMIC-DECLARE-CURSOR
                WHEN SQL-OPEN-STMT
                WHEN SQL-OPEN-WDESC-STMT 
      * Error handling is identical for these statements
                    IF (SQL--ERRORS EQUAL CS-TRUE) AND
                        (SQL--DODECL OF SQL--HANDLES EQUAL CS-TRUE)
                        AND
                        (SQL--COMMAND OF SQL--CUR OF SQL--HANDLES 
                        NOT EQUAL SQL--NULL)
                        CALL "CTBCMDPROPS" USING
                            SQL--COMMAND OF SQL--CONN OF SQL--HANDLES
                            SQL--RETCODE CS-GET
                            CS-CUR-STATUS
                            SQL--CURSTATUS
                            CS-UNUSED
                            SQL--NULL SQL--NULL
      * do nothing if there is no cursor attached to handle
                        IF (SQL--RETCODE EQUAL CS-SUCCEED)
                            CALL "SQLMASKAND" USING
                            SQL--RETCODE SQL--CURSTATUS CS-CURSTAT-NONE
                            IF SQL--RETCODE NOT GREATER THAN 0
                                PERFORM SQL--CTDEALLOC-CURS
                            END-IF
                        END-IF
                        IF (SQL--RETCODE EQUAL CS-SUCCEED)
                            CALL "CTBCMDDROP" USING
                                SQL--COMMAND OF SQL--CUR OF SQL--HANDLES
                                SQL--RETCODE
                            MOVE SQL--NULL TO SQL--COMMAND OF 
                                SQL--CUR OF SQL--HANDLES
                        END-IF
                    END-IF
      * No errors: DYNAMIC-DECLARE-CURSOR -specific handling
      * Store dynamic cursor name
      * Set statement name and length in curdata
                    IF (SQL--ERRORS EQUAL CS-FALSE) AND
                        (SQL--STMTTYPE OF SQL--HANDLES EQUAL 
                        SQL-DYNAMIC-DECLARE-CURSOR) 
                        MOVE SQL--CURDATA-SIZE TO SQL--BUFLEN OF 
                            SQL--CUR OF SQL--HANDLES
                        MOVE SQL--FNLEN OF SQL--STMTNAME OF 
                            SQL--HANDLES TO SQL--DYNSTMTLEN OF 
                            SQL--CURDATA OF SQL--HANDLES
                        MOVE SQL--FIRST-NAME OF SQL--STMTNAME OF 
                            SQL--HANDLES
                            TO SQL--DYNSTMTNAME OF SQL--CURDATA 
                            OF SQL--HANDLES
                
                        IF SQL--DODECL OF SQL--HANDLES EQUAL CS-TRUE
                            MOVE CS-FALSE TO SQL--THINKEXISTS OF
                            SQL--CURNAME OF SQL--HANDLES
                        ELSE
                            MOVE CS-TRUE TO SQL--THINKEXISTS OF
                            SQL--CURNAME OF SQL--HANDLES
                        END-IF
                        CALL "CSBOBJECTS" USING SQL--CTX OF 
                            SQL--HANDLES
                            SQL--RETCODE CS-SET SQL--CURNAME OF
                            SQL--HANDLES SQL--DUMMY SQL--DUMMY 
                            SQL--CUR OF
                            SQL--HANDLES SQL--STMTDATA OF 
                            SQL--HANDLES
                        IF (SQL--RETCODE EQUAL CS-SUCCEED)
                            MOVE CS-SET TO SQL--OPERATION
                            PERFORM SQL--DYNCUR
                        END-IF
                    END-IF
                    IF (SQL--ERRORS EQUAL CS-FALSE) AND
                        ((SQL--STMTTYPE OF SQL--HANDLES EQUAL 
                        SQL-OPEN-STMT) OR
                        (SQL--STMTTYPE OF SQL--HANDLES EQUAL 
                        SQL-OPEN-WDESC-STMT))
      * No errors: OPEN-*STMT -specific handling
      * Use curstatus variable as temp
                        MOVE SQL--CURDATA-SIZE TO SQL--BUFLEN 
                             OF SQL--CUR OF SQL--HANDLES
                        MOVE CS-FALSE TO SQL--CURSTATUS
                        IF (SQL--PERSISTENT OF SQL--STMTDATA OF
                            SQL--HANDLES EQUAL CS-TRUE) AND
                            (SQL--PARAM OF SQL--STMTDATA OF
                            SQL--HANDLES EQUAL CS-TRUE) 
                            MOVE CS-TRUE TO SQL--CURSTATUS
                        END-IF
                        IF (NOT ((SQL--PERSISTENT OF SQL--STMTDATA OF
                            SQL--HANDLES EQUAL CS-TRUE) AND
                            (SQL--NOREBIND OF SQL--CURDATA OF
                            SQL--HANDLES EQUAL CS-TRUE))) AND 
                            (SQL--BIND OF SQL--STMTDATA OF
                            SQL--HANDLES EQUAL CS-FALSE) 
                            MOVE CS-TRUE TO SQL--CURSTATUS
                            MOVE CS-TRUE TO SQL--BIND OF 
                                SQL--STMTDATA OF SQL--HANDLES 
                        END-IF
                        IF (SQL--DODECL OF SQL--HANDLES EQUAL
                            CS-TRUE) OR (SQL--CURSTATUS 
                            EQUAL CS-TRUE)
      * static cursor declare/open
                            MOVE SQL--CURDATA-SIZE TO SQL--BUFLEN
                                OF SQL--CUR OF SQL--HANDLES
                            IF SQL--DODECL OF SQL--HANDLES EQUAL
                                CS-TRUE 
                                MOVE 0 TO SQL--DYNSTMTLEN OF 
                                    SQL--CURDATA OF SQL--HANDLES
                                MOVE CS-FALSE TO 
                                    SQL--THINKEXISTS OF 
                                    SQL--CURNAME OF SQL--HANDLES
                            ELSE
                                MOVE CS-TRUE TO 
                                    SQL--THINKEXISTS OF 
                                    SQL--CURNAME OF SQL--HANDLES
                            END-IF

                            CALL "CSBOBJECTS" USING SQL--CTX OF 
                                SQL--HANDLES SQL--RETCODE CS-SET 
                                SQL--CURNAME OF SQL--HANDLES 
                                SQL--DUMMY SQL--DUMMY SQL--CUR OF
                                SQL--HANDLES SQL--STMTDATA OF 
                                SQL--HANDLES
                        END-IF
                    END-IF
                 
        
      *         Result set operations
                WHEN SQL-FETCH-STMT
                WHEN SQL-FETCH-IDESC-STMT 
                    IF SQL--RETCODE EQUAL CS-HAFAILOVER
                        MOVE 25019 TO SQL--INTRERR
                    END-IF
                    IF (SQL--ERRORS EQUAL CS-FALSE) AND
                        (SQL--DODECL OF SQL--HANDLES EQUAL CS-TRUE)
                        MOVE SQL--CURDATA-SIZE TO SQL--BUFLEN OF 
                            SQL--CUR OF SQL--HANDLES
                        MOVE CS-TRUE TO SQL--THINKEXISTS OF 
                            SQL--CURNAME OF SQL--HANDLES
                        CALL "CSBOBJECTS" USING SQL--CTX OF 
                            SQL--HANDLES
                            SQL--RETCODE CS-SET SQL--CURNAME OF
                            SQL--HANDLES SQL--DUMMY SQL--DUMMY 
                            SQL--CUR OF
                            SQL--HANDLES SQL--STMTDATA OF 
                            SQL--HANDLES
                    END-IF
        
                WHEN SQL-UPDATE-POSITIONED
                    IF (SQL--ERRORS EQUAL CS-FALSE) AND
                        (SQL--PERSISTENT OF SQL--STMTDATA OF 
                        SQL--HANDLES EQUAL CS-TRUE) AND
                        (SQL--PARAM OF SQL--STMTDATA OF 
                        SQL--HANDLES EQUAL CS-TRUE)
                        MOVE CS-FALSE TO SQL--PARAM OF SQL--STMTDATA OF
                            SQL--HANDLES
                        MOVE SQL--CURDATA-SIZE TO SQL--BUFLEN OF 
                            SQL--CUR OF SQL--HANDLES
                        MOVE CS-TRUE TO SQL--THINKEXISTS OF SQL--CURNAME 
                            OF SQL--HANDLES
                        CALL "CSBOBJECTS" USING SQL--CTX
                            OF SQL--HANDLES
                            SQL--RETCODE CS-SET SQL--CURNAME OF
                            SQL--HANDLES SQL--DUMMY SQL--DUMMY 
                            SQL--CUR OF
                            SQL--HANDLES SQL--STMTDATA OF SQL--HANDLES
                    END-IF

                WHEN SQL-PREPARE
                      IF (SQL--ERRORS EQUAL CS-TRUE) 
                          IF SQL--COMMAND OF SQL--STMT OF 
                              SQL--HANDLES NOT EQUAL SQL--NULL
                              CALL "CTBCMDDROP" USING SQL--COMMAND 
                                  OF SQL--STMT OF SQL--HANDLES 
                                  SQL--RETCODE
                          END-IF
                      ELSE
                          MOVE CS-FALSE TO SQL--THINKEXISTS OF
                              SQL--STMTNAME OF SQL--HANDLES
                          MOVE SQL--STMTDATA-SIZE TO SQL--BUFLEN 
                              OF SQL--STMT OF SQL--HANDLES
                          CALL "CSBOBJECTS" USING SQL--CTX OF
                               SQL--HANDLES SQL--RETCODE CS-SET
                               SQL--STMTNAME OF SQL--HANDLES
                               SQL--DUMMY SQL--DUMMY SQL--STMT OF
                               SQL--HANDLES 
                               SQL--STMTDATA OF SQL--HANDLES
                      END-IF

                WHEN SQL-ALLOC-DESC 
                      IF (SQL--ERRORS EQUAL CS-TRUE) AND
                          (SQL--COMMAND OF SQL--DESC OF SQL--HANDLES 
                          NOT EQUAL SQL--NULL)
                          CONTINUE
                      END-IF

            END-EVALUATE

      * Scan for errors again IF there have been errors
            IF (SQL--RETCODE  NOT EQUAL CS-SUCCEED)
                MOVE 0 TO SQL--WORST-INDEX 
                PERFORM SQL--WORST
            END-IF
            IF SQL--WORST-INDEX > 0
                MOVE CS-SUCCEED TO SQL--RETCODE
                MOVE 1 TO SQL--INTARG
                MOVE CS-GET TO SQL--OPERATION
                MOVE SQL--WORST-INDEX TO SQL--ERRINDEX
                PERFORM SQL--CTBDIAG
            ELSE IF SQL--WORST-INDEX < 0
                SUBTRACT SQL--WORST-INDEX FROM 0 
                        GIVING SQL--WORST-INDEX
                MOVE CS-SUCCEED TO SQL--RETCODE
                MOVE 1 TO SQL--INTARG
                MOVE CS-GET TO SQL--OPERATION
                MOVE SQL--WORST-INDEX TO SQL--ERRINDEX
                PERFORM SQL--CSBDIAG
                ELSE
      * If we get here, there are no errors, and we need to
      * set variables to indicate this.
                    MOVE CS-CLEAR TO SQL--OPERATION
                    MOVE CS-UNUSED TO SQL--ERRINDEX
                    PERFORM SQL--CSBDIAG
                END-IF
            END-IF
            IF ((SQL--STMTTYPE OF SQL--HANDLES EQUAL
                SQL-ANSI-CONNECT) OR
                (SQL--STMTTYPE OF SQL--HANDLES EQUAL
                SQL-ALLOC-DESC) OR
                (SQL--STMTTYPE OF SQL--HANDLES EQUAL
                SQL-NONANSI-CONNECT)) AND
                (SQL--ERRORS EQUAL CS-TRUE)
                IF (SQL--DODECL OF SQL--HANDLES EQUAL CS-TRUE)
                    IF (SQL--COMMAND OF SQL--CONN OF 
                            SQL--HANDLES NOT EQUAL 
                            SQL--NULL)
                    CALL "CTBCMDDROP" USING SQL--COMMAND 
                            OF SQL--CONN OF SQL--HANDLES 
                            SQL--RETCODE
                    MOVE SQL--NULL TO SQL--COMMAND      
                            OF SQL--CONN OF SQL--HANDLES 
                    END-IF
                    IF (SQL--CONNECTION OF SQL--CONN OF 
                            SQL--HANDLES NOT EQUAL 
                            SQL--NULL-CONNECTION)
                    CALL "CTBCONDROP" USING SQL--CONNECTION 
                            OF SQL--CONN OF SQL--HANDLES 
                            SQL--RETCODE
                    MOVE SQL--NULL-CONNECTION TO 
                            SQL--CONNECTION      
                            OF SQL--CONN OF SQL--HANDLES 
                    END-IF
                END-IF
            END-IF.

      ************************************************************
      * SQL--SETINTRERR
      ************************************************************
        SQL--SETINTRERR.
            CALL "SQLRAISEERR" USING 
                    SQL--CTX OF SQL--HANDLES
                    SQL--RETCODE
                    SQL--CONNECTION OF SQL--CONN OF SQL--HANDLES
                    SQL--INTRERR.

      ************************************************************
      * SQL--CTXERR
      * Call Veneer layer function to handle errors
      * (25001,25002,25007)
      ************************************************************
        SQL--CTXERR.
            IF SQL--DO-SQLCA EQUAL "Y"
                MOVE CS-TRUE TO SQL--DO-SQLCA-FLAG
            ELSE
                MOVE CS-FALSE TO SQL--DO-SQLCA-FLAG
            END-IF
            IF SQL--DO-SQLCODE EQUAL "Y"
                MOVE CS-TRUE TO SQL--DO-SQLCODE-FLAG
            ELSE
                MOVE CS-FALSE TO SQL--DO-SQLCODE-FLAG
            END-IF
            IF SQL--DO-SQLSTATE EQUAL "Y"
                MOVE CS-TRUE TO SQL--DO-SQLSTATE-FLAG
            ELSE
                MOVE CS-FALSE TO SQL--DO-SQLSTATE-FLAG
            END-IF

            CALL "SQLCTXERR" USING 
                    SQL--CTX OF SQL--HANDLES
                    SQL--INTRERR
                    SQLCA 
                    SQLCODE 
                    SQLSTATE
                    SQL--DO-SQLCA-FLAG
                    SQL--DO-SQLCODE-FLAG
                    SQL--DO-SQLSTATE-FLAG.

      ************************************************************
      * SQL--CSBDIAG
      ************************************************************
        SQL--CSBDIAG.
            IF SQL--OPERATION EQUAL CS-CLEAR
      * Don't know if we've ever initialized this context, do it now
                CALL "CSBDIAG" USING
                    SQL--CTX OF SQL--HANDLES
                    SQL--RETCODE SQL--COMPILER
                    CS-INIT
                    CS-UNUSED CS-UNUSED SQL--NULL
            END-IF
            IF SQL--DO-SQLCA EQUAL "Y"
                CALL "CSBDIAG" USING SQL--CTX OF SQL--HANDLES
                    SQL--RETCODE
                    SQL--COMPILER
                    SQL--OPERATION SQLCA-TYPE 
                    SQL--ERRINDEX SQLCA
                IF SQL--RETCODE EQUAL CS-FAIL
                    MOVE 25007 TO SQL--INTRERR
                    PERFORM SQL--CTXERR
                END-IF
            END-IF

            IF SQL--DO-SQLCODE EQUAL "Y"
                CALL "CSBDIAG" USING SQL--CTX OF SQL--HANDLES
                    SQL--RETCODE
                    SQL--COMPILER
                    SQL--OPERATION SQLCODE-TYPE 
                    SQL--ERRINDEX SQLCODE
                IF SQL--RETCODE EQUAL CS-FAIL
                    MOVE 25007 TO SQL--INTRERR
                    PERFORM SQL--CTXERR
                END-IF
            END-IF

            IF SQL--DO-SQLSTATE EQUAL "Y"
                CALL "CSBDIAG" USING SQL--CTX OF SQL--HANDLES
                    SQL--RETCODE
                    SQL--COMPILER
                    SQL--OPERATION SQLSTATE-TYPE 
                    SQL--ERRINDEX SQLSTATE
                IF SQL--RETCODE EQUAL CS-FAIL
                    MOVE 25007 TO SQL--INTRERR
                    PERFORM SQL--CTXERR
                END-IF
            END-IF

            IF SQL--RETCODE NOT EQUAL CS-FAIL
                MOVE CS-SUCCEED TO SQL--RETCODE
            END-IF.


      ************************************************************
      * SQL--CTBDIAG
      ************************************************************
        SQL--CTBDIAG.
            IF SQL--OPERATION EQUAL CS-CLEAR
      * Don't know if in-line error handling is still installed
                CALL "CTBDIAG" USING
                    SQL--CONNECTION OF SQL--CONN OF SQL--HANDLES
                    SQL--RETCODE SQL--COMPILER
                    CS-INIT
                    CS-UNUSED CS-UNUSED SQL--NULL
            END-IF
            IF SQL--DO-SQLCA EQUAL "Y"
                CALL "CTBDIAG" USING
                    SQL--CONNECTION OF SQL--CONN OF SQL--HANDLES
                    SQL--RETCODE
                    SQL--COMPILER SQL--OPERATION
                    SQLCA-TYPE SQL--ERRINDEX SQLCA
                IF SQL--RETCODE EQUAL CS-FAIL
                    MOVE 25002 TO SQL--INTRERR
                    PERFORM SQL--CTXERR
                END-IF
            END-IF

            IF SQL--DO-SQLCODE EQUAL "Y"
                CALL "CTBDIAG" USING
                    SQL--CONNECTION OF SQL--CONN OF SQL--HANDLES
                    SQL--RETCODE 
                    SQL--COMPILER SQL--OPERATION
                    SQLCODE-TYPE SQL--ERRINDEX SQLCODE
                IF SQL--RETCODE EQUAL CS-FAIL
                    MOVE 25002 TO SQL--INTRERR
                    PERFORM SQL--CTXERR
                END-IF
            END-IF

            IF SQL--DO-SQLSTATE EQUAL "Y"
                CALL "CTBDIAG" USING
                    SQL--CONNECTION OF SQL--CONN OF SQL--HANDLES
                    SQL--RETCODE 
                    SQL--COMPILER SQL--OPERATION
                    SQLSTATE-TYPE SQL--ERRINDEX SQLSTATE
                IF SQL--RETCODE EQUAL CS-FAIL
                        MOVE 25002 TO SQL--INTRERR
                        PERFORM SQL--CTXERR
                END-IF
            END-IF

            IF (SQL--OPERATION EQUAL CS-GET AND 
                SQL--RETCODE EQUAL CS-NOMSG)
                PERFORM SQL--CSBDIAG
            END-IF.


      ************************************************************
      * SQL--DTCNV
      * looks up either ctlib and ansi data types, returns value 
      ************************************************************
        SQL--DTCNV.
            MOVE CS-FAIL TO SQL--INTARG2
            MOVE 0 TO SQL--FOUND
            IF SQL--OPERATION EQUAL CS-GET
                IF SQL--INTARG EQUAL CS-CHAR-TYPE
                    MOVE 1 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-BINARY-TYPE
                    MOVE -5 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-LONGCHAR-TYPE
                    MOVE -2 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-LONGBINARY-TYPE
                    MOVE -7 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-TEXT-TYPE
                    MOVE -3 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-IMAGE-TYPE
                    MOVE -4 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-XML-TYPE
                    MOVE -12 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-TINYINT-TYPE
                    MOVE -8 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-SMALLINT-TYPE
                    MOVE 5 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-INT-TYPE
                    MOVE 4 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-BIGINT-TYPE
                    MOVE 18 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-USMALLINT-TYPE
                    MOVE 20 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-UINT-TYPE
                    MOVE 21 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-UBIGINT-TYPE
                    MOVE 22 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-REAL-TYPE
                    MOVE 7 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-FLOAT-TYPE
                    MOVE 8 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-BIT-TYPE
                    MOVE 14 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-DATETIME-TYPE
                    MOVE 9 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-DATETIME4-TYPE
                    MOVE -9 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-DATE-TYPE
                    MOVE 16 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-TIME-TYPE
                    MOVE 17 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-MONEY-TYPE
                    MOVE -10 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-MONEY4-TYPE
                    MOVE -11 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-NUMERIC-TYPE
                    MOVE 2 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-DECIMAL-TYPE
                    MOVE 3 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-VARCHAR-TYPE
                    MOVE 12 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL CS-VARBINARY-TYPE
                    MOVE -6 TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
            END-IF

            IF SQL--OPERATION EQUAL CS-SET 
                IF SQL--INTARG EQUAL 1
                    MOVE CS-CHAR-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL -5
                    MOVE CS-BINARY-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL -2 
                    MOVE CS-LONGCHAR-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL -7 
                    MOVE CS-LONGBINARY-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL -3 
                    MOVE CS-TEXT-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL -4 
                    MOVE CS-IMAGE-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL -12 
                    MOVE CS-XML-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL -8 
                    MOVE CS-TINYINT-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL 5 
                    MOVE CS-SMALLINT-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL 4 
                    MOVE CS-INT-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL 18 
                    MOVE CS-BIGINT-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL 20 
                    MOVE CS-USMALLINT-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL 21 
                    MOVE CS-UINT-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL 22 
                    MOVE CS-UBIGINT-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL 7
                    MOVE CS-REAL-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL 8 
                    MOVE CS-FLOAT-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL 14 
                    MOVE CS-BIT-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL 9 
                    MOVE CS-DATETIME-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL -9 
                    MOVE CS-DATETIME4-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL 16 
                    MOVE CS-DATE-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL 17 
                    MOVE CS-TIME-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL -10 
                    MOVE CS-MONEY-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL -11 
                    MOVE CS-MONEY4-TYPE TO SQL--INTARG2

                END-IF
                IF SQL--INTARG EQUAL 2 
                    MOVE CS-NUMERIC-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL 3
                    MOVE CS-DECIMAL-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL 12 
                    MOVE CS-VARCHAR-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
                IF SQL--INTARG EQUAL -6 
                    MOVE CS-VARBINARY-TYPE TO SQL--INTARG2
                    MOVE 1 TO SQL--FOUND
                END-IF
            END-IF

            IF SQL--FOUND EQUAL 0 
                MOVE 25011 TO SQL--INTRERR
                PERFORM SQL--SETINTRERR
                MOVE CS-ILLEGAL-TYPE TO SQL--INTARG2
            END-IF.
      ************************************************************
      * SQL--WORST
      * Find the most salient error message.
      ************************************************************
       SQL--WORST.
            MOVE 0 TO SQL--ERRINDEX
            MOVE 0 TO SQL--WARNINDEX
            MOVE 0 TO SQL--INFOINDEX
            MOVE CS-SUCCEED TO SQL--RETCODE
      * Look for errors in the cs_diag queue.
            CALL "CSBDIAG" USING SQL--CTX OF SQL--HANDLES
                SQL--RETCODE
                SQL--COMPILER
                CS-STATUS CS-CLIENTMSG-TYPE 
                CS-UNUSED SQL--NUMMSGS
            MOVE CS-FALSE TO SQL--ERRFOUND
            PERFORM VARYING SQL--MSGNUM FROM 1 BY 1 
                UNTIL SQL--MSGNUM > SQL--NUMMSGS OR
                SQL--RETCODE NOT EQUAL CS-SUCCEED
                OR SQL--ERRFOUND EQUAL CS-TRUE
                    CALL "CSBDIAG" USING SQL--CTX OF SQL--HANDLES
                        SQL--RETCODE
                        SQL--COMPILER
                        CS-GET SQLCODE-TYPE 
                        SQL--MSGNUM SQL--SQLCODE
                    IF SQL--SQLCODE < 0
                        MULTIPLY SQL--MSGNUM BY -1 
                            GIVING SQL--ERRINDEX
                        MOVE CS-TRUE TO SQL--ERRFOUND
                    ELSE IF SQL--SQLCODE > 0 AND SQL--WARNINDEX EQUAL 0
                            MULTIPLY SQL--MSGNUM BY -1 
                                GIVING SQL--WARNINDEX
                        ELSE IF SQL--INFOINDEX EQUAL 0
                                MOVE -1 TO SQL--INFOINDEX
                             END-IF
                        END-IF
                    END-IF
            END-PERFORM
      * We want to stop looking if we have an error already.
      * Look for errors in the ct_diag queue.
            IF SQL--ERRINDEX EQUAL 0 
                AND SQL--HAVE-CONN EQUAL CS-TRUE
                CALL "CTBDIAG" USING 
                    SQL--CONNECTION OF SQL--CONN OF SQL--HANDLES
                    SQL--RETCODE
                    SQL--COMPILER
                    CS-STATUS CS-ALLMSG-TYPE 
                    CS-UNUSED SQL--NUMMSGS
                MOVE CS-FALSE TO SQL--ERRFOUND
                PERFORM VARYING SQL--MSGNUM FROM 1 BY 1 
                    UNTIL SQL--MSGNUM > SQL--NUMMSGS OR
                    SQL--RETCODE NOT EQUAL CS-SUCCEED
                    OR SQL--ERRFOUND EQUAL CS-TRUE
                        CALL "CTBDIAG" USING 
                            SQL--CONNECTION OF SQL--CONN OF 
                                SQL--HANDLES
                            SQL--RETCODE
                            SQL--COMPILER
                            CS-GET SQLCODE-TYPE 
                            SQL--MSGNUM SQL--SQLCODE
                        IF SQL--SQLCODE < 0
                            MOVE SQL--MSGNUM TO SQL--ERRINDEX
                            MOVE CS-TRUE TO SQL--ERRFOUND
                        ELSE IF SQL--SQLCODE > 0 
                                AND SQL--WARNINDEX EQUAL 0
                                MOVE SQL--MSGNUM TO SQL--WARNINDEX
                                ELSE IF SQL--INFOINDEX EQUAL 0
                                    MOVE 1 TO SQL--INFOINDEX
                                END-IF
                            END-IF
                        END-IF
                END-PERFORM
            END-IF
            IF SQL--ERRINDEX NOT EQUAL 0 
                MOVE CS-TRUE TO SQL--ERRORS
                MOVE SQL--ERRINDEX TO SQL--WORST-INDEX
            ELSE IF SQL--WARNINDEX NOT EQUAL 0 
                    MOVE CS-FALSE TO SQL--ERRORS
                    MOVE SQL--WARNINDEX TO SQL--WORST-INDEX
                ELSE 
                    MOVE SQL--INFOINDEX TO SQL--WORST-INDEX
                    MOVE CS-FALSE TO SQL--ERRORS
                END-IF
            END-IF.

      ************************************************************
      * SQL--LAST
      * This paragraph in conjuction with the SQL--PROTECT prevent the
      * unintentional execution of the rest of these paragraphs.
      * DO NOT ADD ANY ADDITIONAL PARAGRAPHS BELOW THIS ONE!
      ************************************************************
        SQL--LAST.
            CONTINUE.

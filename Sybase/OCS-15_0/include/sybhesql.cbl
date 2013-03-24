      * SYBHESQL.CBL
      *    contains SQL variable declarations

      * Error handling variables and table
        01 SQL--INTRERR         pic s9(9) comp-5 value is 0.
      * Operation types
        01 SQL--CREATE pic s9(9) comp-5 value is 101.
        01 SQL--GET pic s9(9) comp-5 value is 102.
        01 SQL--DESTROY pic s9(9) comp-5 value is 103.
      * special "Userdata" object-type for keeping a list of
      * dynamic cursor which have been declared upon dynamic statements
        01 SQL--STMT-CURSOR-TABLE pic s9(9) comp-5 value is 99.
        01 SQL--STMT-CMD-TABLE pic s9(9) comp-5 value is 96.
      * aggregate data group to hold ESQL state information
        01 SQL--HANDLES.
           05 SQL--CTX  pic s9(9) comp-5 value is 0.
           05 SQL--CONNNAME.
              10 SQL--THINKEXISTS pic s9(9) comp-5 value is 0.
              10 SQL--OBJECT-TYPE pic s9(9) comp-5 value is 1.
              10 SQL--LAST-NAME pic x(256).
              10 SQL--LNLEN pic s9(9) comp-5 value is -99999.
              10 SQL--FIRST-NAME pic x(256).
              10 SQL--FNLEN pic s9(9) comp-5 value is -99999.
              10 SQL--SCOPE pic s9(9) comp-5 value is 0.
              10 SQL--SCOPELEN pic s9(9) comp-5 value is -99999.
              10 SQL--THREAD pic s9(9) comp-5 value is 0.
              10 SQL--THREADLEN pic s9(9) comp-5 value is -99999.
           05 SQL--CONN.
              10 SQL--ACTUALLYEXISTS pic s9(9) comp-5 value is 0.
              10 SQL--CONNECTION pic s9(9) comp-5 value is 0.
              10 SQL--COMMAND pic s9(9) comp-5 value is 0.
              10 SQL--BUFFER pic s9(9) comp-5 value is 0.
              10 SQL--BUFLEN pic s9(9) comp-5 value is -99999.
           05 SQL--CURRENTNAME.
              10 SQL--THINKEXISTS pic s9(9) comp-5 value is 0.
              10 SQL--OBJECT-TYPE pic s9(9) comp-5 value is 4.
              10 SQL--LAST-NAME pic x(256).
              10 SQL--LNLEN pic s9(9) comp-5 value is -99999.
              10 SQL--FIRST-NAME pic x(256).
              10 SQL--FNLEN pic s9(9) comp-5 value is -99999.
              10 SQL--SCOPE pic s9(9) comp-5 value is 0.
              10 SQL--SCOPELEN pic s9(9) comp-5 value is -99999.
              10 SQL--THREAD pic s9(9) comp-5 value is 0.
              10 SQL--THREADLEN pic s9(9) comp-5 value is -99999.
           05 SQL--CURRENT.
              10 SQL--ACTUALLYEXISTS pic s9(9) comp-5 value is 0.
              10 SQL--CONNECTION pic s9(9) comp-5 value is 0.
              10 SQL--COMMAND pic s9(9) comp-5 value is 0.
              10 SQL--BUFFER pic s9(9) comp-5 value is 0.
              10 SQL--BUFLEN pic s9(9) comp-5 value is -99999.
           05 SQL--CURNAME.
              10 SQL--THINKEXISTS pic s9(9) comp-5 value is 0.
              10 SQL--OBJECT-TYPE pic s9(9) comp-5 value is 2.
              10 SQL--LAST-NAME pic x(256).
              10 SQL--LNLEN pic s9(9) comp-5 value is -99999.
              10 SQL--FIRST-NAME pic x(256).
              10 SQL--FNLEN pic s9(9) comp-5 value is -99999.
              10 SQL--SCOPE pic s9(9) comp-5 value is 0.
              10 SQL--SCOPELEN pic s9(9) comp-5 value is -99999.
              10 SQL--THREAD pic s9(9) comp-5 value is 0.
              10 SQL--THREADLEN pic s9(9) comp-5 value is -99999.
           05 SQL--CUR.
              10 SQL--ACTUALLYEXISTS pic s9(9) comp-5 value is 0.
              10 SQL--CONNECTION pic s9(9) comp-5 value is 0.
              10 SQL--COMMAND pic s9(9) comp-5 value is 0.
              10 SQL--BUFFER pic s9(9) comp-5 value is 0.
              10 SQL--BUFLEN pic s9(9) comp-5 value is 4.
           05 SQL--STMTNAME.
              10 SQL--THINKEXISTS pic s9(9) comp-5 value is 0.
              10 SQL--OBJECT-TYPE pic s9(9) comp-5 value is 3.
              10 SQL--LAST-NAME pic x(256).
              10 SQL--LNLEN pic s9(9) comp-5 value is -99999.
              10 SQL--FIRST-NAME pic x(256).
              10 SQL--FNLEN pic s9(9) comp-5 value is -99999.
              10 SQL--SCOPE pic s9(9) comp-5 value is 0.
              10 SQL--SCOPELEN pic s9(9) comp-5 value is -99999.
              10 SQL--THREAD pic s9(9) comp-5 value is 0.
              10 SQL--THREADLEN pic s9(9) comp-5 value is -99999.
           05 SQL--STMT.
              10 SQL--ACTUALLYEXISTS pic s9(9) comp-5 value is 0.
              10 SQL--CONNECTION pic s9(9) comp-5 value is 0.
              10 SQL--COMMAND pic s9(9) comp-5 value is 0.
              10 SQL--BUFFER pic s9(9) comp-5 value is 0.
              10 SQL--BUFLEN pic s9(9) comp-5 value is -99999.
           05 SQL--DESCNAME.
              10 SQL--THINKEXISTS pic s9(9) comp-5 value is 0.
              10 SQL--OBJECT-TYPE pic s9(9) comp-5 value is 98.
              10 SQL--LAST-NAME pic x(256).
              10 SQL--LNLEN pic s9(9) comp-5 value is -99999.
              10 SQL--FIRST-NAME pic x(256).
              10 SQL--FNLEN pic s9(9) comp-5 value is -99999.
              10 SQL--SCOPE pic s9(9) comp-5 value is 0.
              10 SQL--SCOPELEN pic s9(9) comp-5 value is -99999.
              10 SQL--THREAD pic s9(9) comp-5 value is 0.
              10 SQL--THREADLEN pic s9(9) comp-5 value is -99999.
           05 SQL--DESC.
              10 SQL--ACTUALLYEXISTS pic s9(9) comp-5 value is 0.
              10 SQL--CONNECTION pic s9(9) comp-5 value is 0.
              10 SQL--COMMAND pic s9(9) comp-5 value is 0.
              10 SQL--BUFFER pic s9(9) comp-5 value is 0.
              10 SQL--BUFLEN pic s9(9) comp-5 value is -99999.
           05 SQL--STMT-CUR-NAME.
              10 SQL--THINKEXISTS pic s9(9) comp-5 value is 0.
              10 SQL--OBJECT-TYPE pic s9(9) comp-5 value is 99.
              10 SQL--LAST-NAME pic x(256).
              10 SQL--LNLEN pic s9(9) comp-5 value is -99999.
              10 SQL--FIRST-NAME pic x(256).
              10 SQL--FNLEN pic s9(9) comp-5 value is -99999.
              10 SQL--SCOPE pic s9(9) comp-5 value is 0.
              10 SQL--SCOPELEN pic s9(9) comp-5 value is -99999.
              10 SQL--THREAD pic s9(9) comp-5 value is 0.
              10 SQL--THREADLEN pic s9(9) comp-5 value is -99999.
           05 SQL--STMT-CUR.
              10 SQL--ACTUALLYEXISTS pic s9(9) comp-5 value is 0.
              10 SQL--CONNECTION pic s9(9) comp-5 value is 0.
              10 SQL--COMMAND pic s9(9) comp-5 value is 0.
              10 SQL--BUFFER pic s9(9) comp-5 value is 0.
              10 SQL--BUFLEN pic s9(9) comp-5 value is -99999.
           05 SQL--STMT-CMD-NAME.
              10 SQL--THINKEXISTS pic s9(9) comp-5 value is 0.
              10 SQL--OBJECT-TYPE pic s9(9) comp-5 value is 96.
              10 SQL--LAST-NAME pic x(256).
              10 SQL--LNLEN pic s9(9) comp-5 value is -99999.
              10 SQL--FIRST-NAME pic x(256).
              10 SQL--FNLEN pic s9(9) comp-5 value is -99999.
              10 SQL--SCOPE pic s9(9) comp-5 value is 0.
              10 SQL--SCOPELEN pic s9(9) comp-5 value is -99999.
              10 SQL--THREAD pic s9(9) comp-5 value is 0.
              10 SQL--THREADLEN pic s9(9) comp-5 value is -99999.
           05 SQL--STMT-CMD.
              10 SQL--ACTUALLYEXISTS pic s9(9) comp-5 value is 0.
              10 SQL--CONNECTION pic s9(9) comp-5 value is 0.
              10 SQL--COMMAND pic s9(9) comp-5 value is 0.
              10 SQL--BUFFER pic s9(9) comp-5 value is 0.
              10 SQL--BUFLEN pic s9(9) comp-5 value is -99999.
           05 SQL--DESC-CONN-NAME.
              10 SQL--THINKEXISTS pic s9(9) comp-5 value is 0.
              10 SQL--OBJECT-TYPE pic s9(9) comp-5 value is 95.
              10 SQL--LAST-NAME pic x(256).
              10 SQL--LNLEN pic s9(9) comp-5 value is -99999.
              10 SQL--FIRST-NAME pic x(256).
              10 SQL--FNLEN pic s9(9) comp-5 value is -99999.
              10 SQL--SCOPE pic s9(9) comp-5 value is 0.
              10 SQL--SCOPELEN pic s9(9) comp-5 value is -99999.
              10 SQL--THREAD pic s9(9) comp-5 value is 0.
              10 SQL--THREADLEN pic s9(9) comp-5 value is -99999.
           05 SQL--DESC-CONN.
              10 SQL--ACTUALLYEXISTS pic s9(9) comp-5 value is 0.
              10 SQL--CONNECTION pic s9(9) comp-5 value is 0.
              10 SQL--COMMAND pic s9(9) comp-5 value is 0.
              10 SQL--BUFFER pic s9(9) comp-5 value is 0.
              10 SQL--BUFLEN pic s9(9) comp-5 value is -99999.
           05 SQL--STMTID pic x(256).
           05 SQL--STMTIDLEN pic s9(9) comp-5 value is -99999.
           05 SQL--STMTTYPE pic s9(9) comp-5 value is -99999.
           05 SQL--STMTDATA.
              10 SQL--PARAM pic s9(9) comp-5 value is 1.
              10 SQL--BIND pic s9(9) comp-5 value is 1.
              10 SQL--PERSISTENT pic s9(9) comp-5 value is 0.
           05 SQL--MOREDATA.
              10 SQL--CURDATA.
                 20 SQL--NOREBIND pic s9(9) comp-5 value is 0.
                 20 SQL--DYNSTMTNAME pic x(256).
                 20 SQL--DYNSTMTLEN pic s9(9) comp-5 value is 0.
           05 SQL--DODECL pic s9(9) comp-5 value is 0.
           05 SQL--DESCSIZE pic s9(9) comp-5 value is 1.

        01 SQL--DFMTUTIL.
           05 SQL--NM              PIC X(256).    
           05 SQL--NMLEN           PIC S9(9) comp-5 value is 0.    
           05 SQL--DATATYPE        PIC S9(9) comp-5 value is 100.    
           05 SQL--FORMAT          PIC S9(9) comp-5 value is 1.    
           05 SQL--MAXLENGTH       PIC S9(9) comp-5 value is 0.    
           05 SQL--SCALE           PIC S9(9) comp-5 value is 0.    
           05 SQL--PRECISION       PIC S9(9) comp-5 value is 18.    
           05 SQL--STTUS           PIC S9(9) comp-5 value is 0.    
           05 SQL--COUNT           PIC S9(9) comp-5 value is 1.    
           05 SQL--USERTYPE        PIC S9(9) comp-5 value is 0.    
           05 SQL--LOCALE          PIC S9(9) comp-5 value is 0.

        01 SQL--DFMTDFMT-NAME.
           05 SQL--NM              PIC X(256).    
           05 SQL--NMLEN           PIC S9(9) comp-5 value is 0.    
           05 SQL--DATATYPE        PIC S9(9) comp-5 value is 100.    
           05 SQL--FORMAT          PIC S9(9) comp-5 value is 1.    
           05 SQL--MAXLENGTH       PIC S9(9) comp-5 value is 0.    
           05 SQL--SCALE           PIC S9(9) comp-5 value is 0.    
           05 SQL--PRECISION       PIC S9(9) comp-5 value is 18.    
           05 SQL--STTUS           PIC S9(9) comp-5 value is 0.    
           05 SQL--COUNT           PIC S9(9) comp-5 value is 1.    
           05 SQL--USERTYPE        PIC S9(9) comp-5 value is 0.    
           05 SQL--LOCALE          PIC S9(9) comp-5 value is 0.
        01 SQL--NULL-CONTEXT pic s9(9) comp-5 value is 0.
        01 SQL--NULL-CONNECTION pic s9(9) comp-5 value is 0.
        01 SQL--NULL pic s9(9) comp-5 value is 0.

        01 SQL--ROWSREAD pic s9(9) comp-5.
        01 SQL--RESTYPE pic s9(9) comp-5.
        01 SQL--OUTLEN pic s9(9) comp-5.
        01 SQL--RETCODE pic s9(9) comp-5 value is -1.
        01 SQL--ZEROIND pic s9(9) comp-5 value is -0.
        01 SQL--LEVEL3 pic s9(9) comp-5 value is 3.
        01 SQL--SMALLINTARG pic s9(4) comp-5.
        01 SQL--INTARG pic s9(9) comp-5.
        01 SQL--INTARG2 pic s9(9) comp-5.
        01 SQL--INTARG3 pic s9(9) COMP-5 value is 0.
        01 SQL--INTARG4 pic s9(9) COMP-5 value is 0.
        01 SQL--INTARG5 pic s9(9) COMP-5 value is 0.
        01 SQL--DUMMY pic s9(9) comp-5.
        01 SQL--ACTION pic s9(9) comp-5.
        01 SQL--OPERATION pic s9(9) comp-5.
        01 SQL--CMDTEXT-LEN pic s9(9) comp-5.
        01 SQL--OBJTYPE pic s9(9) comp-5.
        01 SQL--DO-CANCEL pic s9(9) comp-5.
        01 SQL--LOOPVAR1  pic s9(9) comp-5.
        01 SQL--FOUND     pic s9(9) comp-5.
        01 SQL--RESLOOP   pic s9(9) comp-5.
        01 SQL--MSGFUNC   pic s9(9) comp-5.

        01 SQL--CMDTEXT pic x(255).
        01 SQL--SAVERET pic s9(9) comp-5.

        01 SQL--INTBUF  pic s9(9) comp-5.

        01 SQL--STATE-LNE.
            05 SQL--HEAD PIC X(2) VALUE IS "00".
            05 SQL--REST PIC X(3) VALUE IS LOW-VALUES.
        01 SQL--LOW-SQLSTATE-NONERROR 
            REDEFINES SQL--STATE-LNE PIC X(5).
        01 SQL--STATE-HNE.
            05 SQL--HEAD PIC X(2) VALUE IS "02".
            05 SQL--REST PIC X(3) VALUE IS HIGH-VALUES.
        01 SQL--HIGH-SQLSTATE-NONERROR 
            REDEFINES SQL--STATE-HNE PIC X(5).

        01 SQL--STATE-LW.
            05 SQL--HEAD PIC X(2) VALUE IS "01".
            05 SQL--REST PIC X(3) VALUE IS LOW-VALUES.
        01 SQL--LOW-SQLSTATE-WARNING 
            REDEFINES SQL--STATE-LW PIC X(5).
        01 SQL--STATE-HW.
            05 SQL--HEAD PIC X(2) VALUE IS "01".
            05 SQL--REST PIC X(3) VALUE IS HIGH-VALUES.
        01 SQL--HIGH-SQLSTATE-WARNING 
            REDEFINES SQL--STATE-HW PIC X(5).

        01 SQL--STATE-LNF.
            05 SQL--HEAD PIC X(2) VALUE IS "02".
            05 SQL--REST PIC X(3) VALUE IS LOW-VALUES.
        01 SQL--LOW-SQLSTATE-NOTFOUND 
            REDEFINES SQL--STATE-LNF PIC X(5).
        01 SQL--STATE-HNF.
            05 SQL--HEAD PIC X(2) VALUE IS "02".
            05 SQL--REST PIC X(3) VALUE IS HIGH-VALUES.
        01 SQL--HIGH-SQLSTATE-NOTFOUND 
            REDEFINES SQL--STATE-HNF PIC X(5).
        01 SQL--WORST-INDEX PIC S9(9) comp-5 VALUE IS 0.
        01 SQL--HAVE-CONN PIC S9(9) comp-5 VALUE IS 0.
        01 SQL--MSGNUM PIC S9(9) comp-5.
        01 SQL--NUMMSGS PIC S9(9) comp-5.
        01 SQL--ERRINDEX PIC S9(9) comp-5.
        01 SQL--WARNINDEX PIC S9(9) comp-5.
        01 SQL--INFOINDEX PIC S9(9) comp-5.
        01 SQL--SQLCODE PIC S9(9) comp-5.
        01 SQL--ERRFOUND PIC S9(9) comp-5.
        01 SQL--MUTEX  pic s9(9) comp-5 value is 0.
        01 SQL--CURSTATUS PIC S9(9) comp-5.
        01 SQL--ISCURRENT PIC S9(9) comp-5 value is 0.
        01 SQL--ERRORS PIC S9(9) comp-5 value is 0.
        01 SQL--DO-SQLCA-FLAG pic s9(9) comp-5.
        01 SQL--DO-SQLCODE-FLAG pic s9(9) comp-5.
        01 SQL--DO-SQLSTATE-FLAG pic s9(9) comp-5.
      * value for sizeof curData structure
        01 SQL--CURDATA-SIZE pic s9(9) comp-5 value is 152.
        01 SQL--STMTDATA-SIZE pic s9(9) comp-5 value is 12.
        01 SQL--NAME-SIZE pic s9(9) comp-5 value is 256.
        01 SQL--POINTER-SIZE pic s9(9) comp-5 value is 4.
        01 SQL--CS-VERSION pic s9(9) comp-5.
        01 UNKNOWN-STMT pic s9(9) comp-5 value is 0.
        01 SQL-MISC pic s9(9) comp-5 value is 1.
        01 SQL-ALLOC-DESC pic s9(9) comp-5 value is 2.
        01 SQL-ANSI-CONNECT pic s9(9) comp-5 value is 3.
        01 SQL-BDCL-SCT pic s9(9) comp-5 value is 4.
        01 SQL-TRANS pic s9(9) comp-5 value is 5.
        01 SQL-CLOSE-STMT pic s9(9) comp-5 value is 6.
        01 SQL-DEALLOCATE-DESCRIPTOR pic s9(9) comp-5 value is 7.
        01 SQL-DEALLOCATE-PREPARE pic s9(9) comp-5 value is 8.
        01 SQL-DECLARE-CURSOR pic s9(9) comp-5 value is 9.
        01 SQL-DECLARE-CURSOR-PROC pic s9(9) comp-5 value is 10.
        01 SQL-DELETE-POSITIONED pic s9(9) comp-5 value is 11.
        01 SQL-DELETE-SEARCHED pic s9(9) comp-5 value is 12.
        01 SQL-DESCRIBE-IN pic s9(9) comp-5 value is 13.
        01 SQL-DESCRIBE-OUT pic s9(9) comp-5 value is 14.
        01 SQL-DISCONNECT pic s9(9) comp-5 value is 15.
        01 SQL-DISCONNECT-ALL pic s9(9) comp-5 value is 16.
        01 SQL-DYNAMIC-DECLARE-CURSOR pic s9(9) comp-5 value is 17.
        01 SQL-EDCL-SCT pic s9(9) comp-5 value is 18.
        01 SQL-EXECUTE-IMMEDIATE pic s9(9) comp-5 value is 19.
        01 SQL-EXECUTE-PROCEDURE pic s9(9) comp-5 value is 20.
        01 SQL-EXECUTE pic s9(9) comp-5 value is 21.
        01 SQL-EXIT-STMT pic s9(9) comp-5 value is 22.
        01 SQL-FETCH-IDESC-STMT pic s9(9) comp-5 value is 23.
        01 SQL-FETCH-STMT pic s9(9) comp-5 value is 24.
        01 SQL-GET-DESCRIPTOR pic s9(9) comp-5 value is 25.
        01 SQL-GET-DIAGNOSTICS pic s9(9) comp-5 value is 26.
        01 SQL-INCL-FILE pic s9(9) comp-5 value is 27.
        01 SQL-INCL-SQLCA pic s9(9) comp-5 value is 28.
        01 SQL-INCL-TBLVIEW pic s9(9) comp-5 value is 29.
        01 SQL-INIT-STMT pic s9(9) comp-5 value is 30.
        01 SQL-INSERT-STMT pic s9(9) comp-5 value is 31.
        01 SQL-NONANSI-CONNECT pic s9(9) comp-5 value is 32.
        01 SQL-OPEN-STMT pic s9(9) comp-5 value is 33.
        01 SQL-OPEN-WDESC-STMT pic s9(9) comp-5 value is 34.
        01 SQL-PREPARE pic s9(9) comp-5 value is 35.
        01 SQL-PREPARE-TRANS pic s9(9) comp-5 value is 36.
        01 SQL-SELECT-STMT pic s9(9) comp-5 value is 37.
        01 SQL-SET-DESCRIPTOR pic s9(9) comp-5 value is 38.
        01 SQL-SET-CONNECTION pic s9(9) comp-5 value is 39.
        01 SQL-SET-DIAGNOSTIC-COUNT pic s9(9) comp-5 value is 40.
        01 SQL-THREAD-EXIT-STMT pic s9(9) comp-5 value is 41.
        01 SQL-UPDATE-POSITIONED pic s9(9) comp-5 value is 42.
        01 SQL-UPDATE-SEARCHED pic s9(9) comp-5 value is 43.
        01 SQL-WHENEVER-ERROR pic s9(9) comp-5 value is 44.
        01 SQL-WHENEVER-WARNING pic s9(9) comp-5 value is 45.
        01 SQL-WHENEVER-NOTFOUND pic s9(9) comp-5 value is 46.
        01 SQL-BEGIN-PROG pic s9(9) comp-5 value is 47.
        01 SQL-END-PROG pic s9(9) comp-5 value is 48.
        01 SQL-DECL pic s9(9) comp-5 value is 49.
        01 SQL-INCL-SQLDA pic s9(9) comp-5 value is 50.
        01 SQL-DEALLOCATE-CURSOR-STMT pic s9(9) comp-5 value is 51.
        01 MAX-SQL-STMT pic s9(9) comp-5 value is 52.

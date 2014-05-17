/*
 * AsemonSQLException.java
 *
 * Created on 24 novembre 2007, 12:12
 *
 * To change this template, choose Tools | Template Manager
 * and open the template in the editor.
 */

package asemon_logger;
import java.sql.*;

/**
 *
 * @author jp
 */
public class AsemonSQLException extends SQLException 
{
  private String CnxType;
  private String Module;


  public AsemonSQLException(String message, String SQLState, int vendorCode, String CnxType, String Module)
  {
    super(message,SQLState,vendorCode);
    this.CnxType = CnxType;  
    this.Module = Module;  
  }

  public String getCnxType()
  {
    return CnxType;
  }

  public String getModule()
  {
    return Module;
  }
}
/*
* <p>ColumnDescriptor</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2004</p>
* @version 2.6.4
*
* Class used to store some info about each columns in the metric
*/
package asemon_logger;

public class ColumnDescriptor {



      String colname;            // Name of the column
      boolean computeDiff;       // true if difference with previous sample must be computed for the column
      String className;          // Classname of the column
      String typeName;          // SQL type name of the column
      boolean isInteger;
      boolean isDouble;
      boolean isFloat;
      boolean isLong;
      boolean isBigDecimal;
      int colType;
      boolean ignoreThisColForSave;
      int displaySize;


      ColumnDescriptor (String col) {
          colname = col;
          computeDiff = false;
          className = null;
          typeName = null;
          isInteger = false;
          isDouble = false;
          isFloat = false;
          isLong = false;
          isBigDecimal = false;
          ignoreThisColForSave = false;
      }

      void setType(int t) {
          colType = t;
      }

      int getType() {
          return colType;
      }

      void setTypeName(String tn) {
          typeName = tn;
      }
      
      void setClass(String cls) {
          className = cls;
          if(cls.equals("java.lang.Integer")) isInteger=true;
          if(cls.equals("java.lang.Double")) isDouble=true;
          if(cls.equals("java.lang.Float")) isFloat=true;
          if(cls.equals("java.lang.Long")) isLong=true;
          if(cls.equals("java.math.BigDecimal")) isBigDecimal=true;
      }
      boolean isInteger() { return isInteger;}
      boolean isDouble() { return isDouble;}
      boolean isFloat() { return isFloat;}
      boolean isLong() { return isLong;}
      boolean isBigDecimal() { return isBigDecimal;}
      boolean isIgnoredForSave() { return ignoreThisColForSave;}
      void setIgnoreForSave () { ignoreThisColForSave = true; }
  }

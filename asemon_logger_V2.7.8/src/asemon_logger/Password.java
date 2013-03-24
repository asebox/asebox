/**
* <p>Password</p>
* <p>Asemon_logger : get password if not entered on the command line</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2004</p>
* @version 2.7.5
*/

package asemon_logger;
import java.util.Arrays;
//import java.io.*;
import java.io.Console;

public class Password {

// OLD code :
//   public static String getPassword() {
//    String pass = "";
//    try {
//        Eraser eraser = new Eraser();
//        System.out.print("Password? (Ignore the first *) :  ");
//        eraser.start();
//        BufferedReader stdin = new BufferedReader(new InputStreamReader(System.in));
//        pass = stdin.readLine();
//        eraser.halt();
//        // kill the unwanted '*'
//        // comment out the next line to see what I mean
//        System.out.print("\b");
//        //System.out.println("Password: '" + pass + "'");
//    }
//    catch (Exception e){
//    }
//    return pass;
//  }
// NEW code :
     public static String getPassword() {
         Console console = System.console();
         if (console==null) {
             Asemon_logger.printmess("Cannot ask password when asemon is started in background");
             System.exit(1);
         }
         //read the password, without echoing the output
         char[] password = console.readPassword("Password ? ");
         String pass = new String(password);
         Arrays.fill(password, ' ');

         return pass;

     }


}

class Eraser extends Thread {
 private boolean shouldRun = true;

 public void run() {
  while (shouldRun) {
       System.out.print("\b*");
  }
 }

 public synchronized void halt() {
  shouldRun = false;
 }
}
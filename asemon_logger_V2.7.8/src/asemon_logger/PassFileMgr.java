/**
* <p>Asemon_logger</p>
* <p>Asemon_logger :  PassFileMgr class</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2004</p>
* @version 2.6.0
*/

package asemon_logger;
import java.io.*;

public class PassFileMgr extends Object
{

private		String 	fileName=null;
private 	CrypterTool	crypter;

//------------------------------------------------------
public PassFileMgr(String fn)
{
	try
	{
		fileName = new String(fn);
		crypter=new CrypterTool(8, this.getClass().toString());
	}
	catch (java.lang.Exception e)
	{
		e.printStackTrace();
	}
}

//------------------------------------------------------
public synchronized String getPassword(String srv, String login)
{
	BufferedReader	in;
	String		s;

	String clearPw=null;
	try
	{
		in = new BufferedReader(new FileReader(fileName));
		while ((s=in.readLine()) != null)
		{
			if ( s.startsWith("--" + srv +";" + login + ";"))
			{
                // Found an encrypted password
				String hexCryptPasswd =	s.substring( ("--" + srv +";" + login + ";").length(), s.length() );
				byte[] byteClearPw = crypter.decrypt(hexStringToBytes(hexCryptPasswd));
				if (byteClearPw != null) clearPw = new String(byteClearPw);
				in.close();
                if (byteClearPw == null) {
                    // Bad password encryption, delete this line
                    delPassword(srv, login);
                }
				return(clearPw);
			}

			if ( s.startsWith(srv +";" + login + ";"))
			{
                // Found a clear password (not yet encrypted)
				clearPw =	s.substring( (srv +";" + login + ";").length(), s.length() );
				in.close();
                // Encrypt this password in the password file
                updPassword (srv, login, clearPw);
				return(clearPw);
			}


        }
		in.close();
	}

	// If file not found, skip
	catch (java.io.FileNotFoundException e) {}
	catch (java.lang.Exception e)
	{
		e.printStackTrace();
	}
	return(null);
}

//-------------------------------------------------------
// Encrypt all passwords not yet encrypted
// Description of the passwords file :
// --srv;login;yyyy : encrypted password for (srv;login;) is yyyy
// srv;login;xxxx : unencrypted password for (srv;login;) is xxxx
public synchronized boolean encryptFile ()
{
	PrintWriter	out;
	BufferedReader	in;
	String s;
	String tmp="tmp";
	boolean clearPwFound=false;

	try
	{
        // open passwords file
		in = new BufferedReader(new FileReader(fileName));
		
		// open temporary file
		out = new PrintWriter(
		  new BufferedOutputStream(
		   new FileOutputStream(tmp)));

		while ((s=in.readLine()) != null)
		{
			if ( (s.startsWith("--")) || (s.startsWith("#")) )
				out.println(s);
			else
			{
				//xxx yyy
				clearPwFound=true;
				int i=s.lastIndexOf(";");
				if (i == -1)
				{
					System.out.println("PassFileMgr : Error encryptFile, pw file, bad line format (s)");
					return(false);
				}
				byte[] byteClearPw = (s.substring(i+1,s.length())).getBytes();
				byte[] byteCryptPw = crypter.encrypt(byteClearPw);
				
				String hexCryptPw = bytesToHexString(byteCryptPw);

				// Debug
				//System.out.println("Avant convert : 0x"+hexCryptPw);
				//byte[] byteData = hexStringToBytes(hexCryptPw);
				//System.out.println("Apres convert : 0x"+bytesToHexString(byteData));
				
				out.println("--" + s.substring(0,i) + ";" + hexCryptPw);
			}
		}
		out.close();
		in.close();

		if (clearPwFound)
		{
			// Recopie ds le fichier d'origine
			out = new PrintWriter(
			  new BufferedOutputStream(
			   new FileOutputStream(fileName)));
			in = new BufferedReader(new FileReader(tmp));
			while ((s=in.readLine()) != null)
				out.println(s);
			out.close();
			in.close();
		}
		File f = new File(tmp);
		f.delete();
		return(true);
	}
	// If file not found, skip
	catch (java.io.FileNotFoundException e) {}
	catch (java.lang.Exception e)
	{
		e.printStackTrace();
	}
	return (false);
}



public void addPassword (String srv, String login, String clearPw)
{
	PrintWriter	out;
	String s;

	try
	{
		out = new PrintWriter(
		  new BufferedOutputStream(
		   new FileOutputStream(fileName, true)));

                byte[] byteClearPw = clearPw.getBytes();
		byte[] byteCryptPw = crypter.encrypt(byteClearPw);
		
		String hexCryptPw = bytesToHexString(byteCryptPw);

		// Debug
		//System.out.println("Avant convert : 0x"+hexCryptPw);
		//byte[] byteData = hexStringToBytes(hexCryptPw);
		//System.out.println("Apres convert : 0x"+bytesToHexString(byteData));
		
		out.println("--" + srv + ";" + login + ";"+ hexCryptPw);

		out.close();

	}
	catch (java.lang.Exception e)
	{
		e.printStackTrace();
	}
	return ;
}



public synchronized void updPassword (String srv, String login, String clearPw)
{
	PrintWriter	out;
	BufferedReader	in;
	String s;
	String tmp="tmp";
	boolean clearPwFound=false;

	try
	{
                // open passwords file
		in = new BufferedReader(new FileReader(fileName));
		
		// open temporary file
		out = new PrintWriter(
		  new BufferedOutputStream(
		   new FileOutputStream(tmp)));

		while ((s=in.readLine()) != null)
		{
			if (  s.startsWith("--"+srv+";"+login+";") || s.startsWith(srv+";"+login+";")  ) {
			    clearPwFound = true;
                byte[] byteClearPw = clearPw.getBytes();
                byte[] byteCryptPw = crypter.encrypt(byteClearPw);
		            
                String hexCryptPw = bytesToHexString(byteCryptPw);
                            		            
                out.println("--" + srv + ";" + login + ";"+ hexCryptPw);
				
			}
			else
				out.println(s);

		}
		out.close();
		in.close();

		if (clearPwFound)
		{
			// Recopie ds le fichier d'origine
			out = new PrintWriter(
			  new BufferedOutputStream(
			   new FileOutputStream(fileName)));
			in = new BufferedReader(new FileReader(tmp));
			while ((s=in.readLine()) != null)
				out.println(s);
			out.close();
			in.close();
		}
		File f = new File(tmp);
		f.delete();
		return;
	}
	catch (java.lang.Exception e)
	{
		e.printStackTrace();
	}
	return ;
}


public synchronized void delPassword (String srv, String login)
{
	PrintWriter	out;
	BufferedReader	in;
	String s;
	String tmp="tmp";
	boolean pwFound=false;

	try
	{
        // open passwords file
		in = new BufferedReader(new FileReader(fileName));

		// open temporary file
		out = new PrintWriter(
		  new BufferedOutputStream(
		   new FileOutputStream(tmp)));

		while ((s=in.readLine()) != null)
		{
			if ( s.startsWith("--"+srv+";"+login+";")   ) {
			    pwFound = true;
                // don't write this line in the temp file
			}
			else
				out.println(s);

		}
		out.close();
		in.close();

		if (pwFound)
		{
			// Recopie ds le fichier d'origine
			out = new PrintWriter(
			  new BufferedOutputStream(
			   new FileOutputStream(fileName)));
			in = new BufferedReader(new FileReader(tmp));
			while ((s=in.readLine()) != null)
				out.println(s);
			out.close();
			in.close();
		}
		File f = new File(tmp);
		f.delete();
		return;
	}
	catch (java.lang.Exception e)
	{
		e.printStackTrace();
	}
	return ;
}




    /**
       Gives the hexadecimal representation of the values in a
       <code>byte</code> array.

       @param buffer the byte array.

       @return a String object containing the hexadecimal representation.

       <br /><br />
    */
    public static String bytesToHexString(byte[] buffer)
    {
        String hex = new String("");
        for(int j = 0; j  < buffer.length; ++j) {
            int i = (int) buffer[j] & 0x000000ff;
            hex += hexStrings_[i];
        }
        return hex;
    }


    /**
       Gives the byte[] representation of the values in a
       <code>string</code>.

       @param a String object containing the hexadecimal representation.
       @return the byte array.

       <br /><br />
    */
    public static byte[] hexStringToBytes(String hex)
    {
        byte[] buffer = new byte[hex.length()/2];
        for(int j = 0; j  < hex.length()/2; ++j) {
            buffer[j] = (byte) ( Character.digit(hex.charAt(j*2),16)*16 + Character.digit(hex.charAt(j*2 + 1),16) );
        }
        return buffer;
    }
    
    private static String[] hexStrings_ = {
"00","01","02","03","04","05","06","07","08","09","0a","0b","0c","0d","0e","0f",
"10","11","12","13","14","15","16","17","18","19","1a","1b","1c","1d","1e","1f",
"20","21","22","23","24","25","26","27","28","29","2a","2b","2c","2d","2e","2f",
"30","31","32","33","34","35","36","37","38","39","3a","3b","3c","3d","3e","3f",
"40","41","42","43","44","45","46","47","48","49","4a","4b","4c","4d","4e","4f",
"50","51","52","53","54","55","56","57","58","59","5a","5b","5c","5d","5e","5f",
"60","61","62","63","64","65","66","67","68","69","6a","6b","6c","6d","6e","6f",
"70","71","72","73","74","75","76","77","78","79","7a","7b","7c","7d","7e","7f",
"80","81","82","83","84","85","86","87","88","89","8a","8b","8c","8d","8e","8f",
"90","91","92","93","94","95","96","97","98","99","9a","9b","9c","9d","9e","9f",
"a0","a1","a2","a3","a4","a5","a6","a7","a8","a9","aa","ab","ac","ad","ae","af",
"b0","b1","b2","b3","b4","b5","b6","b7","b8","b9","ba","bb","bc","bd","be","bf",
"c0","c1","c2","c3","c4","c5","c6","c7","c8","c9","ca","cb","cc","cd","ce","cf",
"d0","d1","d2","d3","d4","d5","d6","d7","d8","d9","da","db","dc","dd","de","df",
"e0","e1","e2","e3","e4","e5","e6","e7","e8","e9","ea","eb","ec","ed","ee","ef",
"f0","f1","f2","f3","f4","f5","f6","f7","f8","f9","fa","fb","fc","fd","fe","ff"
    };


}


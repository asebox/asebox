/**
* <p>Asemon_logger</p>
* <p>Asemon_logger :  CrypterTool class</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2004</p>
* @version 1.3.2
*/

package asemon_logger;
import java.util.*;
import javax.crypto.spec.*;
import javax.crypto.*;

public class CrypterTool extends Object
{

Cipher		pbeEncryptCipher, pbeDecryptCipher;

//------------------------------------------------------
public CrypterTool(int count,String key)
{
	byte sel6 = (byte)0x21;
	byte sel2 = (byte)0xa2;
	byte sel4 = (byte)0x17;

	byte[] salt = {
		(byte)0xc9, (byte)0x73, (byte)0x21, (byte)0x8c,
		(byte)0x7e, (byte)0xc8, (byte)0xee, (byte)0x99
	};

	byte sel5 = (byte)0x2c;
	byte sel1 = (byte)0x21;
	byte sel7 = (byte)0x19;
	byte sel3 = (byte)0x04;
	byte sel8 = (byte)0x0b;

        PBEKeySpec pbeKeySpec;
        PBEParameterSpec pbeParamSpec;
        SecretKeyFactory keyFac;
	
	try
	{
		// Create PBE parameter set
		byte[] mySalt = new byte[8];
		mySalt[7]=sel7;
		mySalt[5]=sel3;
		mySalt[1]=sel1;
		mySalt[2]=sel4;
		mySalt[6]=sel5;
		mySalt[3]=sel2;
		mySalt[0]=sel6;
		mySalt[4]=sel8;
		pbeParamSpec = new PBEParameterSpec( mySalt, count);
		pbeKeySpec = new PBEKeySpec((key+this.getClass().toString()).toCharArray());
		keyFac = SecretKeyFactory.getInstance("PBEWithMD5AndDES");
		SecretKey pbeKey = keyFac.generateSecret(pbeKeySpec);

		pbeEncryptCipher = Cipher.getInstance("PBEWithMD5AndDES");
		pbeDecryptCipher = Cipher.getInstance("PBEWithMD5AndDES");

		pbeEncryptCipher.init(Cipher.ENCRYPT_MODE, pbeKey, pbeParamSpec);
		pbeDecryptCipher.init(Cipher.DECRYPT_MODE, pbeKey, pbeParamSpec);
	}
	catch (java.lang.Exception e)
	{
		e.printStackTrace();
	}
}

//------------------------------------------------------
public CrypterTool(int count)
{
	this(count,"20400101");
}

//-------------------------------------------------------
public byte[] encrypt (byte[] in)
{
	byte[] out=null ;

	try
	{
		out = pbeEncryptCipher.doFinal(in);
	}
	catch (java.lang.Exception e)
	{
		e.printStackTrace();
	}
	return out;
}

//-------------------------------------------------------
public byte[] decrypt (byte[] in)
{
	byte[] out=null ;

	try
	{
		out = pbeDecryptCipher.doFinal(in);
	}
	catch (java.lang.Exception e)
	{
		System.out.println("Password decrypt error. Bad key or bad encryption");
		//e.printStackTrace();
	}
	return out;
}

}


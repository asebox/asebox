<?php
   $name = $_REQUEST['name'] ;
   $password = $_REQUEST['password'] ;
   session_start();
   $_SESSION["asebox_contact"]  = "";
   $_SESSION["asebox_fullname"] = "";

   if (( $name == 'guest')    && ($password != 'xxx')) { $f='Guest';  } 
   if (( $name == '53077')    && ($password != 'xxx')) { $f='Arnaud de la Porte du Theil';  } 
   if (( $name == '159122')    && ($password != 'xxx')) { $f='Cedrik TAURY';  } 
   if (( $name == '225279')    && ($password != 'xxx')) { $f='Frederic BAUDREE';  } 
   if (( $name == '141554')    && ($password != 'xxx')) { $f='Laurent BEDE';  } 
   if (( $name == '363876')    && ($password != 'xxx')) { $f='Abdel Haq EL GHAZI';  } 
   if (( $name == '848423')    && ($password != 'xxx')) { $f='AhamedKabeer SABIAHAMMED';  } 
   if (( $name == '858071')    && ($password != 'xxx')) { $f='Ali WEHEBI';  } 
   if (( $name == '52741')    && ($password != 'xxx')) { $f='Anja RAZATOVO';  } 
   if (( $name == '833513')    && ($password != 'xxx')) { $f='Bruno ABREU';  } 
   if (( $name == '35075')    && ($password != 'xxx')) { $f='Bruno HOUDOUIN';  } 
   if (( $name == '208638')    && ($password != 'xxx')) { $f='Christophe CHEVALLIER';  } 
   if (( $name == '509878')    && ($password != 'xxx')) { $f='DAHER Alain';  } 
   if (( $name == '366500')    && ($password != 'xxx')) { $f='Denis GOUDET';  } 
   if (( $name == '920661')    && ($password != 'xxx')) { $f='Dineshkumar SIVASHANMUGAM';  } 
   if (( $name == '543179')    && ($password != 'xxx')) { $f='Dixon Jose PAULOSE SELVI';  } 
   if (( $name == '952379')    && ($password != 'xxx')) { $f='EL HAMDI Fouad';  } 
   if (( $name == '950527')    && ($password != 'xxx')) { $f='Ganesh KODARKAR';  } 
   if (( $name == '233396')    && ($password != 'xxx')) { $f='Gauthier DEVILLE';  } 
   if (( $name == '370063')    && ($password != 'xxx')) { $f='Gildas LE GOFF';  } 
   if (( $name == '289346')    && ($password != 'xxx')) { $f='Guillaume COUVEZ';  } 
   if (( $name == '462075')    && ($password != 'xxx')) { $f='Guillaume REBOUTE';  } 
   if (( $name == '442998')    && ($password != 'xxx')) { $f='Guy KIM';  } 
   if (( $name == '36935')    && ($password != 'xxx')) { $f='Guy LECORE';  } 
   if (( $name == '328163')    && ($password != 'xxx')) { $f='Herve Peillon';  } 
   if (( $name == '907524')    && ($password != 'xxx')) { $f='Jean-Louis JACQUEL';  } 
   if (( $name == '864400')    && ($password != 'xxx')) { $f='Junior GUNTHER';  } 
   if (( $name == '585052')    && ($password != 'xxx')) { $f='Kamalakannan PURUSHOTTHAMAN';  } 
   if (( $name == '927489')    && ($password != 'xxx')) { $f='Kanthimathi KAILASANATHAN';  } 
   if (( $name == '313346')    && ($password != 'xxx')) { $f='Karim Garchi';  } 
   if (( $name == '960575')    && ($password != 'xxx')) { $f='KIBANGOU Demuth-Roland';  } 
   if (( $name == '928174')    && ($password != 'xxx')) { $f='MAHESH SALLA';  } 
   if (( $name == '363873')    && ($password != 'xxx')) { $f='Makhlouf GUENAOUI';  } 
   if (( $name == '320294')    && ($password != 'xxx')) { $f='Manuel MONTERO';  } 
   if (( $name == '970218')    && ($password != 'xxx')) { $f='MARA Idrissa';  } 
   if (( $name == '438634')    && ($password != 'xxx')) { $f='Marc GELLER';  } 
   if (( $name == '867744')    && ($password != 'xxx')) { $f='Mitra NAIENIE';  } 
   if (( $name == '929535')    && ($password != 'xxx')) { $f='Orlando FERMIN';  } 
   if (( $name == '944472')    && ($password != 'xxx')) { $f='Prakash MOHAN';  } 
   if (( $name == '932386')    && ($password != 'xxx')) { $f='Rahul KANKANE';  } 
   if (( $name == '844989')    && ($password != 'xxx')) { $f='Regis RABY';  } 
   if (( $name == '934719')    && ($password != 'xxx')) { $f='Ricardo NOBRE';  } 
   if (( $name == '318519')    && ($password != 'xxx')) { $f='Roch KOUAKAM';  } 
   if (( $name == '877587')    && ($password != 'xxx')) { $f='Ruben CATARRUNAS';  } 
   if (( $name == '932395')    && ($password != 'xxx')) { $f='Saikat DEY';  } 
   if (( $name == '820484')    && ($password != 'xxx')) { $f='Sylvie AZEMAR';  } 
   if (( $name == '935548')    && ($password != 'xxx')) { $f='Vengatesan GANESAN';  } 
   if (( $name == '657667')    && ($password != 'xxx')) { $f='Vinh Phan Thong';  } 
   if (( $name == '952613')    && ($password != 'xxx')) { $f='YEM TOLEN Janvier';  } 
   if (( $name == '828964')    && ($password != 'xxx')) { $f='Ana Lucia HORTA';  } 
   if (( $name == '944968')    && ($password != 'xxx')) { $f='Aristide FLANDRIN';  } 
   if (( $name == '335812')    && ($password != 'xxx')) { $f='Christophe MAUGALEM';  } 
   if (( $name == '380698')    && ($password != 'xxx')) { $f='David BAOSSADE';  } 
   if (( $name == '188080')    && ($password != 'xxx')) { $f='Dominique BOERO';  } 
   if (( $name == '922448')    && ($password != 'xxx')) { $f='Eduardo PICADO';  } 
   if (( $name == '923450')    && ($password != 'xxx')) { $f='Frederico MONTEIRO';  } 
   if (( $name == '936081')    && ($password != 'xxx')) { $f='Mario PRATAS FERREIRA';  } 
   if (( $name == '832378')    && ($password != 'xxx')) { $f='Rui Jorge RODRIGUES';  } 
   if (( $name == '253453')    && ($password != 'xxx')) { $f='Cyril SEBAH';  } 
   if (( $name == '828964')    && ($password != 'xxx')) { $f='Emad ABUSHAKRA';  } 
   if (( $name == '944968')    && ($password != 'xxx')) { $f='Emmanuel BARGAS';  } 
   if (( $name == '653949')    && ($password != 'xxx')) { $f='Geoff STAMP';  } 
   if (( $name == '188080')    && ($password != 'xxx')) { $f='Jean Francois VITEUR';  } 
   if (( $name == '922448')    && ($password != 'xxx')) { $f='Joao Costa LOPES';  } 
   if (( $name == '976295')    && ($password != 'xxx')) { $f='Jose MOURAO';  } 
   if (( $name == '923450')    && ($password != 'xxx')) { $f='Luis MONTEIRO';  } 
   if (( $name == '936081')    && ($password != 'xxx')) { $f='Norredine MABROUKI';  } 
   if (( $name == '832378')    && ($password != 'xxx')) { $f='Pedro SANTOS';  } 
   if (( $name == '380698')    && ($password != 'xxx')) { $f='Philippe PEUCH';  } 
   if (( $name == '930729')    && ($password != 'xxx')) { $f='Sergio SANTOS LOPES';  } 
   if (( $name == '946721')    && ($password != 'xxx')) { $f='Valter MACHADO';  } 
   if (( $name == '160318')    && ($password != 'xxx')) { $f='Dinesh SELVAJAYAM';  } 
   if (( $name == '161958')    && ($password != 'xxx')) { $f='Laurent CASTAN';  } 
   if (( $name == '892794')    && ($password != 'xxx')) { $f='Adil CHAGRAOUI';  } 
   if (( $name == '874304')    && ($password != 'xxx')) { $f='Adrien VEIGAS';  } 
   if (( $name == '352345')    && ($password != 'xxx')) { $f='Alessandra PALOSCHI';  } 
   if (( $name == '411082')    && ($password != 'xxx')) { $f='alexandre BRANDON';  } 
   if (( $name == '472171')    && ($password != 'xxx')) { $f='alexis ADITTANE';  } 
   if (( $name == '531484')    && ($password != 'xxx')) { $f='Anish JOHN';  } 
   if (( $name == '834140')    && ($password != 'xxx')) { $f='Anne Bellec';  } 
   if (( $name == '372678')    && ($password != 'xxx')) { $f='Barbara ANGELUCCI';  } 
   if (( $name == '936137')    && ($password != 'xxx')) { $f='BASTOS';  } 
   if (( $name == '801282')    && ($password != 'xxx')) { $f='Biswajit BURAGOHAIN';  } 
   if (( $name == '386173')    && ($password != 'xxx')) { $f='Bocande BERTRAND';  } 
   if (( $name == '405589')    && ($password != 'xxx')) { $f='Donatello BALLABIO';  } 
   if (( $name == '557887')    && ($password != 'xxx')) { $f='Donatello BALLABIO';  } 
   if (( $name == '353542')    && ($password != 'xxx')) { $f='Emanuele VOLPONI';  } 
   if (( $name == '929055')    && ($password != 'xxx')) { $f='Emeric MONNET';  } 
   if (( $name == '824313')    && ($password != 'xxx')) { $f='Erwan JAOUEN';  } 
   if (( $name == '796911')    && ($password != 'xxx')) { $f='Erwan MARTIN';  } 
   if (( $name == '965387')    && ($password != 'xxx')) { $f='Frank HILLARD';  } 
   if (( $name == '935466')    && ($password != 'xxx')) { $f='Geetha MANI';  } 
   if (( $name == '553469')    && ($password != 'xxx')) { $f='Govindaraju SRINIVASAN';  } 
   if (( $name == '909993')    && ($password != 'xxx')) { $f='Jacques BITTON';  } 
   if (( $name == '215433')    && ($password != 'xxx')) { $f='John Paul OUKO';  } 
   if (( $name == '867546')    && ($password != 'xxx')) { $f='Justin MARCHAND';  } 
   if (( $name == '359223')    && ($password != 'xxx')) { $f='Karine CURFS';  } 
   if (( $name == '542793')    && ($password != 'xxx')) { $f='M-ANVAR Anvar';  } 
   if (( $name == '457400')    && ($password != 'xxx')) { $f='Marie BENHAYOUN';  } 
   if (( $name == '939958')    && ($password != 'xxx')) { $f='MARQUES';  } 
   if (( $name == '387073')    && ($password != 'xxx')) { $f='Mauro MARTINELLI';  } 
   if (( $name == '467043')    && ($password != 'xxx')) { $f='Michael BOUKHOBZA';  } 
   if (( $name == '328991')    && ($password != 'xxx')) { $f='Michael JEAN';  } 
   if (( $name == '375639')    && ($password != 'xxx')) { $f='Olga VOROTNIKOVA';  } 
   if (( $name == '693310')    && ($password != 'xxx')) { $f='Olivier MARCADET';  } 
   if (( $name == '310043')    && ($password != 'xxx')) { $f='Ottavia FERRARI';  } 
   if (( $name == '149863')    && ($password != 'xxx')) { $f='Philippe HUBERT';  } 
   if (( $name == '440264')    && ($password != 'xxx')) { $f='Roberto VITALE';  } 
   if (( $name == '829136')    && ($password != 'xxx')) { $f='Safwan ATTRASH';  } 
   if (( $name == '348783')    && ($password != 'xxx')) { $f='SALIM Ouirdane';  } 
   if (( $name == '665190')    && ($password != 'xxx')) { $f='Saranya BALAGURUSAMY';  } 
   if (( $name == '463462')    && ($password != 'xxx')) { $f='Thierry GOLASKEVITCH';  } 
   if (( $name == '472623')    && ($password != 'xxx')) { $f='Thomas MICHEL';  } 
   if (( $name == '302119')    && ($password != 'xxx')) { $f='Tiziana MARCANTONIO';  } 
   if (( $name == '866956')    && ($password != 'xxx')) { $f='Vinoth Kannan BALAKRISHNAN';  } 
   if (( $name == '935614')    && ($password != 'xxx')) { $f='Abdelhamid BOUSSIF';  } 
   if (( $name == '474531')    && ($password != 'xxx')) { $f='Ahmed JOUAD IRP';  } 
   if (( $name == '53525')    && ($password != 'xxx')) { $f='AIT ZIAD Abderrahman';  } 
   if (( $name == '939217')    && ($password != 'xxx')) { $f='Andrea SILVA';  } 
   if (( $name == '358827')    && ($password != 'xxx')) { $f='Antoinette KHOURY';  } 
   if (( $name == '938794')    && ($password != 'xxx')) { $f='Arivazhagan MOHAN';  } 
   if (( $name == '425099')    && ($password != 'xxx')) { $f='Artur HENRIQUES';  } 
   if (( $name == '967277')    && ($password != 'xxx')) { $f='Aurelien CHEVREUX';  } 
   if (( $name == '52792')    && ($password != 'xxx')) { $f='BASLE Bertrand';  } 
   if (( $name == '959984')    && ($password != 'xxx')) { $f='Bensouna KRELIFAOUI';  } 
   if (( $name == '829299')    && ($password != 'xxx')) { $f='Carlos Ataide';  } 
   if (( $name == '144460')    && ($password != 'xxx')) { $f='Catherine CLEANTE';  } 
   if (( $name == '52896')    && ($password != 'xxx')) { $f='Chan Francois';  } 
   if (( $name == '52794')    && ($password != 'xxx')) { $f='CHARPENTIER Sylvain';  } 
   if (( $name == '53524')    && ($password != 'xxx')) { $f='Charron Sabine';  } 
   if (( $name == '52887')    && ($password != 'xxx')) { $f='CHAUMONT Olivier';  } 
   if (( $name == '52643')    && ($password != 'xxx')) { $f='David QUERE';  } 
   if (( $name == '348782')    && ($password != 'xxx')) { $f='DE SOUSA Alexandre';  } 
   if (( $name == '556914')    && ($password != 'xxx')) { $f='Emmanuel CARRASSAN';  } 
   if (( $name == '196680')    && ($password != 'xxx')) { $f='Francois PETIT';  } 
   if (( $name == '957488')    && ($password != 'xxx')) { $f='Ganesh SREEKUMAR';  } 
   if (( $name == '144162')    && ($password != 'xxx')) { $f='Gilles CHERTIER';  } 
   if (( $name == '52791')    && ($password != 'xxx')) { $f='GOMES DA ROSA Jean-Marc';  } 
   if (( $name == '897702')    && ($password != 'xxx')) { $f='HAIDARA';  } 
   if (( $name == '363571')    && ($password != 'xxx')) { $f='HILAIRE';  } 
   if (( $name == '919157')    && ($password != 'xxx')) { $f='Himanshu Sood';  } 
   if (( $name == '918774')    && ($password != 'xxx')) { $f='Ilyas AIT TALEB';  } 
   if (( $name == '542169')    && ($password != 'xxx')) { $f='Ismakl SOOBRATTY';  } 
   if (( $name == '52795')    && ($password != 'xxx')) { $f='IVOY Jean-Frangois';  } 
   if (( $name == '349787')    && ($password != 'xxx')) { $f='Jamal El RHAZOUI';  } 
   if (( $name == '836770')    && ($password != 'xxx')) { $f='Joao Nuno MARTINS';  } 
   if (( $name == '795928')    && ($password != 'xxx')) { $f='Joaquim PEDROSO';  } 
   if (( $name == '947396')    && ($password != 'xxx')) { $f='Karthikraj RAJKUMAR';  } 
   if (( $name == '957611')    && ($password != 'xxx')) { $f='Kristian Vasev';  } 
   if (( $name == '973782')    && ($password != 'xxx')) { $f='Lugo PENETRA';  } 
   if (( $name == '845230')    && ($password != 'xxx')) { $f='Mamadou NIAGATE';  } 
   if (( $name == '835382')    && ($password != 'xxx')) { $f='Marcourf HUREL';  } 
   if (( $name == '37728')    && ($password != 'xxx')) { $f='MC DERMOTT Patrick';  } 
   if (( $name == '460292')    && ($password != 'xxx')) { $f='Mohamed Iddtalbe';  } 
   if (( $name == '351171')    && ($password != 'xxx')) { $f='Morade HAFDI';  } 
   if (( $name == '52471')    && ($password != 'xxx')) { $f='MORANGE Nicolas';  } 
   if (( $name == '485307')    && ($password != 'xxx')) { $f='Nicolas Venot';  } 
   if (( $name == '345144')    && ($password != 'xxx')) { $f='Oleksiy CHAGOVETS';  } 
   if (( $name == '829297')    && ($password != 'xxx')) { $f='Pedro MENDES';  } 
   if (( $name == '829295')    && ($password != 'xxx')) { $f='Pedro VAZ';  } 
   if (( $name == '209284')    && ($password != 'xxx')) { $f='PERROTIN Guillaume';  } 
   if (( $name == '562402')    && ($password != 'xxx')) { $f='Pierre-Paul COUKA';  } 
   if (( $name == '479455')    && ($password != 'xxx')) { $f='Pierre-Yves BERRE IRP';  } 
   if (( $name == '919142')    && ($password != 'xxx')) { $f='Praveenkumar Ashok';  } 
   if (( $name == '688834')    && ($password != 'xxx')) { $f='Ranjith Ravi';  } 
   if (( $name == '929563')    && ($password != 'xxx')) { $f='Sarah LOKMAN';  } 
   if (( $name ==  '52799')    && ($password != 'xxx')) { $f='SAURET Neil';  } 
   if (( $name == '188239')    && ($password != 'xxx')) { $f='Sergio ROMERA';  } 
   if (( $name == '470121')    && ($password != 'xxx')) { $f='Sophie CHHIM';  } 
   if (( $name == '942721')    && ($password != 'xxx')) { $f='Sriramachandran RAJARAM';  } 
   if (( $name == '320819')    && ($password != 'xxx')) { $f='Stephane BOISSEAU';  } 
   if (( $name == '929640')    && ($password != 'xxx')) { $f='Stephane OTTONELLO';  } 
   if (( $name == '454672')    && ($password != 'xxx')) { $f='Thomas BERNHARD';  } 
   if (( $name == '680778')    && ($password != 'xxx')) { $f='Ulrich KAMGANG';  } 
   if (( $name == '912097')    && ($password != 'xxx')) { $f='USTINOV';  } 
   if (( $name == '352453')    && ($password != 'xxx')) { $f='Yavuz KAYA';  } 
   if (( $name == '821625')    && ($password != 'xxx')) { $f='Zied BEN DHIAF';  } 
   if (( $name == '859731')    && ($password != 'xxx')) { $f='Balaji RAJASEKAR';  } 
   if (( $name == '542790')    && ($password != 'xxx')) { $f='Balasudhakar CHINNUSAMY';  } 
   if (( $name == '594607')    && ($password != 'xxx')) { $f='Banh LIEUSONG';  } 
   if (( $name == '462999')    && ($password != 'xxx')) { $f='bugnet denis';  } 
   if (( $name == '822983')    && ($password != 'xxx')) { $f='Gurucharan RAJULAPATI';  } 
   if (( $name == '893824')    && ($password != 'xxx')) { $f='Karthik NAGARAJAN';  } 
   if (( $name == '708447')    && ($password != 'xxx')) { $f='Landry THOMAZO';  } 
   if (( $name == '538198')    && ($password != 'xxx')) { $f='Loic Ducoup';  } 
   if (( $name == '712927')    && ($password != 'xxx')) { $f='Narendranadh GUTTIKONDA';  } 
   if (( $name == '633203')    && ($password != 'xxx')) { $f='Nicolas PONTOIZEAU';  } 
   if (( $name == '949490')    && ($password != 'xxx')) { $f='Prashant SRINIVASAN';  } 
   if (( $name == '525316')    && ($password != 'xxx')) { $f='Raja SUBRAMANIAN';  } 
   if (( $name == '851475')    && ($password != 'xxx')) { $f='Rajesh DHANAPAL';  } 
   if (( $name == '592955')    && ($password != 'xxx')) { $f='Romuald SIX';  } 
   if (( $name == '897249')    && ($password != 'xxx')) { $f='Saravanan SHANMUGHAM';  } 
   if (( $name == '859732')    && ($password != 'xxx')) { $f='Somu SUNDARAM';  } 
   if (( $name == '470134')    && ($password != 'xxx')) { $f='Sophie HARAL';  } 
   if (( $name == '955299')    && ($password != 'xxx')) { $f='Sureshkumar RAMASAMY';  } 
   if (( $name == '788902')    && ($password != 'xxx')) { $f='Thomas DOUSSAU';  } 
   if (( $name == '902732')    && ($password != 'xxx')) { $f='Wilfried GARNIER';  } 
   if (( $name == '268137')    && ($password != 'xxx')) { $f='William Rey';  } 
   if (( $name == '211170')    && ($password != 'xxx')) { $f='David QUERE';  } 
   if (( $name == '895650')    && ($password != 'xxx')) { $f='Nohelani CHONFONT';  } 
   if (( $name == '912598')    && ($password != 'xxx')) { $f='Sara DUARTE';  } 
   if (( $name == '843683')    && ($password != 'xxx')) { $f='Susana BARREIROS';  } 
   if (( $name == '496231')    && ($password != 'xxx')) { $f='Wilfried WANDZE';  }    	

   //It's OK
   if ( $f !== "" ) { 	
      $_SESSION["asebox_contact"]  = $name;
      $_SESSION["asebox_fullname"] = $f;
   	
      //header("Location: ../asebox_report/asebox_main.php");
      print "success";
   }
   
?>
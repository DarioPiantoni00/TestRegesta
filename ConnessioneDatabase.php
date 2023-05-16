<?php
	// pagina che effettua la connessione al database e istanzia l'oggetto mysqli (per interagire col db)
	define("SERVER","localhost");
	define("UTENTE","root");
	define("PASSWORD","");
	define("DATABASE","testregesta");
	
	$mysqli=new mysqli(SERVER,UTENTE,PASSWORD,DATABASE);
?>
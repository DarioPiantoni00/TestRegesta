<?php
	require("ConnessioneDatabase.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Visualizza Prodotti</title>
		<link type="text/css" href="Stile.css" rel="stylesheet">	
	</head>
	<body>
		<h1>Visualizza fornitori di un prodotto</h1>
		<h2>Scegli il prodotto tra quelli in elenco</h2>
		<form method="POST" action="index.php">
			Prodotto:
			<select name="prodotto" required>
				<?php
					//leggo dal db tutti i prodotti disponibili
					$stmt=$mysqli->prepare("SELECT
												DISTINCT prodotto.id,prodotto.nome
											FROM 
												prodotto,stock,fornitore
											WHERE
												prodotto.id=stock.idProdotto
											AND
												stock.idFornitore=fornitore.id
											AND
												stock.quantita>0");
					$stmt->execute();
					$stmt->bind_result($idProdotto,$nomeProdotto);
					$stmt->store_result();
					
					while($stmt->fetch()){
						echo "<option value='".$idProdotto."'>".$nomeProdotto."</option>";
					}
					$stmt->close();
				?>
			</select>
			<br>
			<br>
			Quantità:
			<input type="number" name="quantita" step="1" min="1" required>
			<br>
			<br>
			<input type="submit" name="invia" value="Cerca">
		</form>
		
		<?php
			$nFornitori=0;
			$prezzi=array();
			
			//se è stato premuto il bottone di submit allora stampo i fornitori per quel prodotto
			if(isset($_POST["invia"])){
				echo "<h2>Elenco fornitori per il prodotto</h2>";
				
				//leggo dalla POST i valori inseriti
				$prodottoInserito=$_POST["prodotto"];
				$quantitaInserita=$_POST["quantita"];
				
				//leggo dal db tutti i fornitori che hanno il prodotto selezionato
				$stmt=$mysqli->prepare("SELECT
											fornitore.id,fornitore.nome,stock.prezzo,fornitore.giorniSpedizione
										FROM
											fornitore,stock,prodotto
										WHERE
											fornitore.id=stock.idFornitore
										AND
											stock.idProdotto=prodotto.id
										AND
											stock.quantita>=(?)
										AND
											prodotto.id=(?)");
				$stmt->bind_param("ii",$quantitaInserita,$prodottoInserito);
				$stmt->execute();
				$stmt->bind_result($idFornitore,$nomeFornitore,$prezzo,$giorniSpedizione);
				$stmt->store_result();
				
				
				while($stmt->fetch()){
					echo "<p id='".$nFornitori."'>".$nomeFornitore.": ".$prezzo." euro per unità può soddisfare la richiesta con prezzo totale di ";
					$nFornitori++;
					
					$prezzoTotale=$prezzo*$quantitaInserita;
					
					//leggo dal db gli sconti per ogni fornitore per ogni tipologia di sconto (totale,quantita,data)
					$tipoSconti=array("totale","quantita","data");
					
					$scontoTotaleApplicato=false;
					$scontoQuantitaApplicato=false;
					
					for($i=0;$i<count($tipoSconti);$i++){
						$stmtSconti=$mysqli->prepare("SELECT
														sconto.tipo,sconto.quando,sconto.percentuale
													FROM
														sconto,fornitore
													WHERE
														sconto.idFornitore=fornitore.id
													AND
														sconto.tipo=(?)
													AND
														fornitore.id=(?)
													ORDER BY
														sconto.quando DESC");
						$stmtSconti->bind_param("si",$tipoSconti[$i],$idFornitore);
						$stmtSconti->execute();
						$stmtSconti->bind_result($tipoSconto,$quandoSconto,$percentualeSconto);
						$stmtSconti->store_result();

						//per ogni tipologia di sconto verifico se è applicabile a quell'ordine e se non è già stato applicato uno sconto migliore
						while($stmtSconti->fetch()){
							if(strcasecmp($tipoSconto,"totale")==0 && $prezzoTotale>=$quandoSconto && $scontoTotaleApplicato==false){
								$prezzoTotale-=$prezzoTotale*((double)$percentualeSconto/100);
								$scontoTotaleApplicato=true;
							}
			
							if(strcasecmp($tipoSconto,"quantita")==0 && $quantitaInserita>$quandoSconto && $scontoQuantitaApplicato==false){
								$prezzoTotale-=($prezzoTotale*((double)$percentualeSconto/100));
								$scontoQuantitaApplicato=true;
								
							}
							if(strcasecmp($tipoSconto,"data")==0){
								$mese=date("n");
								if($mese==$quandoSconto)
									$prezzoTotale-=$prezzoTotale*((double)$percentualeSconto/100);
							}
						}
					}
					
					//stampo il prezzo scontato e lo aggiungo all'array che servirà per trovare il prezzo migliore
					echo round($prezzoTotale,2)." euro (".$giorniSpedizione." giorni di spedizione)</p>";
					array_push($prezzi,$prezzoTotale);
					$stmtSconti->close();
				}
				
				$stmt->close();
				
				//leggo dal db tutti i fornitori che non hanno il prodotto selezionato
				$stmt=$mysqli->prepare("SELECT
											fornitore.nome
										FROM 
											fornitore
										WHERE
											fornitore.id NOT IN(SELECT 
																	fornitore.id
																FROM
																	fornitore,stock,prodotto
																WHERE
																	fornitore.id=stock.idFornitore
																AND
																	stock.idProdotto=prodotto.id
																AND
																	stock.quantita>=(?)
																AND
																	prodotto.id=(?))");
				$stmt->bind_param("ii",$quantitaInserita,$prodottoInserito);
				$stmt->execute();
				$stmt->bind_result($nomeFornitore);
				$stmt->store_result();
				
				while($stmt->fetch()){
					echo "<p>".$nomeFornitore.": non ha abbastanza unità per il prodotto selezionato</p>";
				}
				
			}

			//trovo il prezzo minore, solo se ho almeno 1 fornitore
			if($nFornitori>0){
				$min=$prezzi[0];
				$indiceMin=0;
				for($i=1;$i<count($prezzi);$i++){
					if($prezzi[$i]<$min){
						
						$min=$prezzi[$i];
						$indiceMin=$i;
					}
				}
				
				echo "<script>document.getElementById('".$indiceMin."').style.fontWeight = 'bold';</script>";
			}
		?>		
	</body>
</html>
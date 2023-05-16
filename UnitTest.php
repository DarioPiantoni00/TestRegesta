<?php
	require("ConnessioneDatabase.php");
	require_once 'PHPUnit/Framework.php';
	
	class UnitTest extends PHPUnit_Framework_TestCase{
		//test per i prodotti nel database (che stampo nel menù a tendina)
		public function testProdotti(){
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
				
				//secondo i dati inseriti dal database questa query ritorna 2 righe
				$this->assertEquals(2,$stmt->num_rows);
				
				$stmt->close();
		}
		
		//test per i fornitori che hanno un determinato prodotto
		public function testFornitori(){
			$stmt=$mysqli->prepare("SELECT
											*
									FROM
										fornitore,stock,prodotto
									WHERE
										fornitore.id=stock.idFornitore
									AND
										stock.idProdotto=prodotto.id
									AND
										stock.quantita>=12
									AND
										prodotto.id=1");
			$stmt->execute();
			$stmt->store_result();
			
			//secondo i dati inseriti dal database questa query ritorna 2 righe
			$this->assertEquals(2,$stmt->num_rows;);
			
			$stmt->close();
		}
		
		//test per i fornitori che non hanno un determinato prodotto
		public function testFornitoriNoProdotto(){
			stmt=$mysqli->prepare("SELECT
										*
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
																stock.quantita>=12
															AND
																prodotto.id=1)");
			$stmt->execute();
			$stmt->store_result();
			
			//secondo i dati inseriti dal database questa query ritorna 1 riga
			$this->assertEquals(1,$stmt->num_rows);
			
			$stmt->close();
		}
		
		//test per il calcolo del prezzo scontato
		public function testCalcoloPrezzoScontato(){
			$prezzoTotale=1548;//prezzo calcolato senza sconti
			
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
												fornitore.id=3
											ORDER BY
												sconto.quando DESC");
				$stmtSconti->bind_param("s",$tipoSconti[$i]);
				$stmtSconti->execute();
				$stmtSconti->bind_result($tipoSconto,$quandoSconto,$percentualeSconto);
				$stmtSconti->store_result();

				
				while($stmtSconti->fetch()){
					if(strcasecmp($tipoSconto,"totale")==0 && $prezzoTotale>=$quandoSconto && $scontoTotaleApplicato==false){
						$prezzoTotale-=$prezzoTotale*((double)$percentualeSconto/100);
						$scontoTotaleApplicato=true;
					}
	
					if(strcasecmp($tipoSconto,"quantita")==0 && $quantitaInserita>=$quandoSconto && $scontoQuantitaApplicato==false){
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
			$stmtSconti->close();
			
			//prezzo che mi aspetto con gli sconti
			$this->assertEqualsWithDelta(1441.188,$prezzoTotale,0.1);
		}
	}
?>
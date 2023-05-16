Il linguaggio utilizzato è il PHP, il software si esegue lanciando la pagina index.php, il file UnitText.php contiene
alcuni unit test che ho realizzato per testare il software. Il file Stile.css contiene alcuni css per rendere
più carina la pagina WEB.

Il database usato è un database relazionale SQL, per lo schema ER vedi il file SchemaER.jpg .
L'export delle tabelle complete dei dati utilizzate per il testing è nel file testregesta.sql . 
L'entità Fornitore è identificata da un id numerico progressivo e rappresenta un fornitore di merce che rifornisce il
negozio. A Fornitore sono collegate le entità Stock e Sconto.
Sconto contiene gli sconti che un fornitore può applicare; uno sconto può essere di 3 tipi: sul totale, sulla quantità
e per un mese dell'anno, la tipologia è indicata dall'attributo "tipo", mentre l'attributo "quando" indica quando
quello sconto sarà da applicare; esempio Sconto tipo="totale", quando=1000, percentuale=5 significa che applico lo
sconto del 5% appena ho un ordine di 1000 euro.
L'entità Stock contiene quanti pezzi ha un fornitore relativamente ad un certo prodotto e a quanto lo vende.
L'entità Prodotto identifica un prodotto con un id numerico progressivo e un nome.

Il file Guida utilizzo software.pdf contiene una piccola guida per utilizzare al meglio il software da me realizzato.
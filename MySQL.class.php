<?php
class MySQL {
	
	var $host = 'localhost';
	var $usr = 'root';
	var $pw = '';
	var $db = 'frete';
	var $sql;
	var $conn;
	var $resultado;

	
	//	Construtor
	function __construct() { }
	
	
	// Conecta-se ao banco de dados e o seleciona
	function connMySQL() {
		
		$this->conn = mysqli_connect( $this->host, $this->usr, $this->pw, $this->db );
		
		if(!$this->conn) {
			echo "<p>Não foi possível conectar-se ao servidor MySQL.</p>\n" .
				 "<p><strong>Erro MySQL: " . mysqli_error() . "</strong></p>\n";
				 exit();
		} elseif (!mysqli_select_db($this->conn, $this->db) ) {
			echo "<p>Não foi possível selecionar o Banco de Dados desejado.</p>\n".
				 "<p><strong>Erro MySQL: " . mysqli_error() . "</strong></p>\n";
				 exit();
		}
		
		/* change character set to utf8 */
		if (!$this->conn->set_charset("utf8")) {
				printf("Error loading character set utf8: %s\n", $this->conn->error);
				exit();
		} else {
				//printf("Current character set: %s\n", $this->conn->character_set_name());
		}

	}
	
	//	Roda a consulta
	function runQuery($sql) {
		$this->connMySQL();
		$this->sql = $sql;
		if($this->resultado = mysqli_query($this->conn, $this->sql)) {
			$_SESSION["last_insert_id"]= mysqli_insert_id($this->conn);//	grava o último id inserido na sessão
			$this->closeConnMySQL();
			return $this->resultado;
		} else {
			//<p>N?o foi poss?vel executar a seguinte instru??o instru??o SQL:</p><p><strong>$sql</strong></p>
			exit("<p>Erro MySQL: " . mysqli_error($this->conn) . "</p>");
			$this->closeConnMySQL();
		}
	}
	
	
	//	Fecha a conexão
	function closeConnMySQL() {
		return mysqli_close($this->conn);
	}
	
}

$mySQL = new MySQL;
?>
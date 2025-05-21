package util;

import java.sql.Connection;

public class Principal {

	public static void main(String[] args) {
		ConnectionFactory fabrica = new ConnectionFactory();
		Connection conexao = fabrica.getConnection();
		if (conexao != null) {
			System.out.println("Conectado com sucesso !!");
		}
		else {
			System.out.println("Não foi possível conectar !");
		}
	}
}
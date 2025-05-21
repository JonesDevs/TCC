package util;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class ConnectionFactory {

	public Connection getConnection() {
		Connection conexao = null;
		String banco = "etec";
		// no Xampp o banco é etec
		String usuario = "root";
		String senha = "";
		// no Xampp a senha é vazia ""
		try {
			conexao = DriverManager.getConnection(
					 "jdbc:mysql://localhost/"+banco,
					 usuario, 
					 senha);
		} catch (SQLException e) {
			System.out.println(e.getMessage());
		} 
		return conexao;		
	}
}
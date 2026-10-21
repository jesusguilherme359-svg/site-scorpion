// db.js
import mysql from "mysql2";

// Cria a conexão com o banco de dados
const connection = mysql.createConnection({
  host: "localhost",      // servidor local do XAMPP
  user: "root",           // usuário padrão do MySQL no XAMPP
  password: "",           // senha vazia (padrão XAMPP)
  database: "db_streetwear"  // nome do banco definido em database/setup.sql
});

// Conecta e testa
connection.connect((err) => {
  if (err) {
    console.error("❌ Erro ao conectar ao MySQL:", err);
    return;
  }
  console.log("✅ Conectado ao banco de dados com sucesso!");
});

export default connection;

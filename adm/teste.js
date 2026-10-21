import connection from "./db.js";

connection.query("SELECT * FROM produtos", (err, results) => {
  if (err) throw err;
  console.log("🧾 Produtos cadastrados:");
  console.log(results);
  connection.end();
});

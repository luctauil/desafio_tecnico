<?php

error_reporting(0);

include 'conn.php';

function executeQuery($query, $params = []) {
    global $host, $dbName, $username, $password;

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        throw new Exception('Failed to connect to the database.');
    }

    try {
        $statement = $pdo->prepare($query);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception("Failed to execute the query. ". $query);
    }
}

function executeInsert($query, $params = []) {
    global $host, $dbName, $username, $password;

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // Em caso de falha na conexão, retorne um erro
        throw new Exception('Failed to connect to the database. '. $query);
    }

    try {
        // Prepara a consulta SQL
        $statement = $pdo->prepare($query);

        // Executa a consulta com os parâmetros fornecidos
        $statement->execute($params);

        // Retorna o ID da última linha inserida
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        // Em caso de erro na consulta, retorne um erro
        throw new Exception('Failed to execute the query.');
    }
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type');

$endpoint = $_GET['endpoint'];
switch ($endpoint) {
    case 'validate_login':
        handleValidateLogin();
        break;
    case 'product':
        handleProducts();
        break;

    case 'product_type':
        handleProductTypes();
        break;

    case 'tax':
        handleTax();
        break;
    
    case 'sale':
        handleSale();
        break;
}

function handleValidateLogin() {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT id FROM users WHERE username = '".$username."' AND password = '".$password."' AND active = 1";

    try {
        $result = executeQuery($query, [
            'username' => $username,
            'password' => $password
        ]);

        if (!empty($result)) {
            session_start();
            $_SESSION['auth'] = true;

        $response = [
                'success' => true,
                'message' => 'Autenticação bem-sucedida'
            ];
        } 
        else {
            $response = [
                'success' => false,
                'message' => 'Usuário ou senha incorretos',
                'query' => $query
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function handleProducts()
{
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            $products = getProducts();
            echo json_encode($products);

            break;
        case 'POST':
            $productName = $_POST['productName'];
            $product_type = $_POST['product_type'];
            $price = $_POST['price'];
            createProduct($productName, $product_type, $price);
            break;
        default:
            handleNotFound();
            break;
    }
}


function handleProductTypes() {
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            $product_types = getProductTypes();
            echo json_encode($product_types);

            break;
        case 'POST':
            $productName = $_POST['productName'];
            createProductType($productName);
            break;
        default:
            handleNotFound();
            break;
    }
}

function handleTax(){
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            $taxes = getTaxes();
            echo json_encode($taxes);
            break;
        case 'POST':
            $product_type = $_POST['product_type'];
            $tax = $_POST['tax'];
            createTax($product_type, $tax);
            break;
    }
}

function handleSale(){
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            $sales = getSales();
            echo json_encode($sales);
            break;
        case 'POST':
            $itens_venda = $_POST['itens_venda'];
            $total_sale = $_POST['total_sale'];
            createSale($itens_venda, $total_sale);
            break;
    }
}

function getProductTypes() {
    $sql = "SELECT name, id FROM product_types";

    $result = executeQuery($sql, []);
    return $result;
}


function getProducts() {
    $sql = "select p.id, p.name, p.price, t.name as type, r.tax_rate from products as p inner join product_types as t on p.product_type_id = t.id inner join tax_rates as r on t.id = r.product_type_id order by p.name";

    $result = executeQuery($sql, []);
    return $result;
}

function getTaxes() 
{
    $sql = "SELECT t.id, p.name, t.tax_rate FROM tax_rates AS t INNER JOIN product_types as p ON p.id = t.product_type_id ORDER BY t.tax_rate desc";
    $result = executeQuery($sql, []);
    return $result;
}

function getSales() 
{
    $sql = "select s.id, s.sale_date, s.total_amount, sum(si.tax_amount) as tax
                from sales as s
                inner join sale_items as si on s.id = si.sale_id
                GROUP BY s.id, s.sale_date, s.total_amount
                order by s.sale_date desc
                limit 0, 100;";
    $result = executeQuery($sql, []);
    return $result;
}

function createProductType($data) {
    $query = "INSERT INTO product_types (name) VALUES (?)";

    try {
        $insertId = executeInsert($query, [$data]);
        echo json_encode(['success' => true, 'insertId' => $insertId]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function createProduct($productName, $product_type, $price) {
    $query = "INSERT INTO products (product_type_id, name, price) VALUES (?, ?, ?)";

    try {
        $insertId = executeInsert($query, [$product_type, $productName, $price]);
        echo json_encode(['success' => true, 'insertId' => $insertId]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function createTax($product_type, $tax){
    $query = "INSERT INTO tax_rates (product_type_id, tax_rate) VALUES (?, ?)";
    
    try {
        $insertId = executeInsert($query, [$product_type, $tax]);
        echo json_encode(['success' => true, 'insertId' => $insertId]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function createSale($itens_venda, $total_sale)
{
    try {
        $itens_venda = json_decode($itens_venda, true);

        $query = "INSERT INTO sales (sale_date, total_amount) VALUES (NOW(), ?);";
        $insertId = executeInsert($query, [$total_sale]);
    
        if (is_array($itens_venda)) {

            foreach ($itens_venda as $item) {

                    $product_id = $item['product_id'];
                    $name = $item['name'];
                    $price = $item['price'];
                    $qtd = $item['qtd'];
                    $total_price = $item['total_price'];
                    $tax_rate = $item['tax_rate'];
                    $tax_item = $item['tax_item'];
        
                    $query_item = "INSERT INTO sale_items (sale_id, product_id, quantity, item_amount, tax_amount)
                        VALUES (?, ?, ?, ?, ?);";
                        $insertIdItem = executeInsert($query_item, [$insertId, $product_id, $qtd, $total_price, $tax_item]);
            }
        } 

        echo json_encode(['success' => true, 'insertId' => $insertId, 'itens_meu_Deus' => $itens_venda, 'entrou_for' => $entrou]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}



?>

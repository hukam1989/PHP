<?php
require "dbconnect.php";
require_once("Rest.inc.php");

class API extends REST {
	public $name;	
	function __construct($name = null) {
	$this->name = $name; 
	}
	
	public function get_name(){		
		return $this->name;
	}
	
	///http://localhost/myphp/api/addData?name=ht&address=441&postcode=175046
	//insert data to db
	public function addData(){			
		if($_POST ["name"] || $_POST["address"] || $_POST['postcode']) {			
		// insert a row
		echo $cname = $_POST['name'];exit;
		$address = $_POST['address'];
		$postcode = $_POST['postcode'];
	
		// prepare sql and bind parameters
		$stmt = $conn->prepare("INSERT INTO customers (cname, address, postcode)
		VALUES (:cname, :address, :postcode)");
		$stmt->bindParam(':cname', $cname);
		$stmt->bindParam(':address', $address);
		$stmt->bindParam(':postcode', $postcode);
		$stmt->execute();		
			echo "New records created successfully";			
		}else{
			echo "Data not inserted.";
		}			
	}	
	// get customer list
	public function getCustomers(){
		global $conn;
		$query = $conn->prepare('SELECT customers.cid as ctid, customers.cname,customers.postcode,orders.oid as orderid,orders.odate as date, orders.cid as orderid FROM customers 
		Left join orders ON orders.cid = customers.cid
		where customers.country = "usa" group by customers.cid desc'
		);
		$query->execute();
		return $query->fetchAll();
	}
	//get order list
	public function orderList(){
		global $conn;
		$_query = $conn->prepare('Select * from orders');
		$_query->execute();
		return  $_query->fetch();					
	}	
}//end class

//inharitance parent class(htcls) to child class(mittu)
class Orders extends API{	
		public function showOrderList(){		
		global $conn;
		$_query = $conn->prepare('Select * from customers where country IN (select country from orders where country ="usa")');
		//$_query = $conn->prepare('Select * from customers where country IN (select country from orders where country ="usa")');
		$_query->execute();
		return  $_query->fetchAll();		
	}
	
	public function myUnion(){
	global $conn;
		$_query_u = $conn->prepare('SELECT * FROM customers
		WHERE country="usa"
		UNION
		SELECT * FROM orders
		WHERE country="usa"
		ORDER BY country');
		$_query_u->execute();
		return  $_query_u->fetchAll();
	}
		
	public function anyALL(){
	global $conn;
		$_query_ALL = $conn->prepare('select cname,address from customers where cid = any (select cid from orders where country="usa")');
		$_query_ALL->execute();
		return  $_query_ALL->fetchAll();		
	}
		
}//end class
?>

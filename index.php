<?php 

header("Content-type:application/json");

$host = "";
$dbusername = "";
$dbpassword = "";
$dbname = "";

$output["records"]=array();
$id="";
$name="";
$email="";

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

if(!$conn){
    die('Could not connect: '.mysqli_connect_errno());
}

//switch requests
$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method)
{
	case 'GET':
		// Retrieve
		if(!empty($_GET["id"]))
		{
			$id=intval($_GET["id"]);
			get($id);
		}
		else
		{
			get();
		}
		break;
	case 'POST':
		// Insert
		insert();
		break;
	case 'PUT':
		// Update
		$id = isset($_GET["id"])?$_GET["id"]:"";
		update($id);
		break;
	case 'DELETE':
		// Delete
		$id = isset($_GET["id"])?$_GET["id"]:"";
		delete($id);
		break;
	default:
		// Invalid Request Method
		header("HTTP/1.0 405 Method Not Allowed");
		break;
}

//get request
function get($id=0)
{
	global $conn, $output;
	$query="SELECT * FROM test";
	if($id != 0)
	{
		$query.=" WHERE id=".$id." LIMIT 1";
	}

	$result = $conn->query($query);

		while($row = $result -> fetch_assoc()){
			extract($row);
			$user_item=array(
				"id" => $id,
				"name" => $name,
				"email" => $email
			);
			array_push($output["records"], $user_item);
		}

	print(json_encode($output["records"], JSON_PRETTY_PRINT));
}

//post request with postman post-data
function insert()
{
	global $conn, $name, $email;
	$name = (isset($_POST['name']))?$_POST['name']:'';
	$email = (isset($_POST['email']))?$_POST['email']:'';
	$query="INSERT INTO test SET name='{$name}', email='{$email}'";
	if($conn->query($query))
	{
		$response=array(
			'status' => 1,
			'status_message' =>'Added Successfully.'
		);
	}
	else
	{
		$response=array(
			'status' => 0,
			'status_message' =>'Addition Failed.'
		);
	}
	header('Content-Type: application/json');
	echo json_encode($response);
}

//put request with postman x-www-form-urlencoded
function update($id)
{
	global $conn, $name, $email;
	parse_str(file_get_contents("php://input"),$post_vars);
	$name=isset($post_vars["name"])?$post_vars["name"]:"";
	$email=isset($post_vars["email"])?$post_vars["email"]:"";
	$query="UPDATE test SET name='{$name}', email='{$email}' WHERE id=".$id;
	if($conn->query($query))
	{
		$response=array(
			'status' => 1,
			'status_message' =>'Updated Successfully.'
		);
	}
	else
	{
		$response=array(
			'status' => 0,
			'status_message' =>'Update Failed.'
		);
	}
	header('Content-Type: application/json');
	echo json_encode($response);
}

//delete request
function delete($id)
{
	global $conn;
	$query="DELETE FROM test WHERE id=".$id;
	if($conn->query($query))
	{
		$response=array(
			'status' => 1,
			'status_message' =>'Deleted Successfully.'
		);
	}
	else
	{
		$response=array(
			'status' => 0,
			'status_message' =>'Deletion Failed.'
		);
	}
	header('Content-Type: application/json');
	echo json_encode($response);
}

$conn->close();

?>
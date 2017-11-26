<?php 
try {
    $db = new PDO('mysql:host=localhost;dbname=guest', 'root', '');
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

function filter($string) {
	$bad  = array("kanker","hoer","tering");
    $string = str_ireplace($bad, "***", $string);
    return $string;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$username = $_POST['username'];
	$message = $_POST['message'];
	
	if(empty($username) || empty($message)) {
		$notice =  '<div class="notice error"> U heeft niet alles ingevuld </div>';
	} elseif(strlen($username) > 20) {
		$notice = '<div class="notice error"> Naam is te lang</div>';
	} else {
		$notice = '<div class="notice success">  Uw bericht is geplaatst</div>';
		$stm = $db->prepare("INSERT INTO message (name, message, date) VALUES (:username, :message, UNIX_TIMESTAMP())");
		$stm->execute([":username" => $username, ":message" => $message]);
	}
}
$sth = $db->prepare('SELECT * FROM message ORDER BY id DESC');
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);
?>
<html>
	<head>
		<title>Gastenboek</title>	
		<link rel="stylesheet" type="text/css" href="css/style.css?<?php echo mt_rand() ?>">
	</head>
	<body>
	<div class="container">
		<form method="post" action="">
			<?php if(!empty($notice)) { echo $notice; } else { echo "<div class='notice default'>Gastenboek (filtered: kanker, hoer, tering)</div>"; } ?>
			<br>
			Naam
			<input type="text" name="username" placeholder="Naam"><br><br>
			Bericht<br>
			<textarea rows="5" cols="22" name="message"></textarea><br><br>
			<input type="submit" name="submit" value="Plaats">
		</form>
		
		<div class="guestbook"> 
		<table style="width:100%">
	
		<?php 
		foreach ($result as $row) {
			print 
			"
			 <tr><td class='guest name'>" . filter($row['name']) . ": </td> <td class='guest message'>" . filter($row['message']) . " </td><td class='guest date'>" . date("Y-m-d h:i:sa", $row['date']) . "</td> </tr>";
		}
		?>
	
		</table>
		</div>
	</div>
	</body>
</html>
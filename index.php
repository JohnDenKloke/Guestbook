<?php
	$host = "localhost";
	$dbname = "guestbook";
	$username = "Guestbook";
	$password="123456";
	$dsn = "mysql:host=$host;dbname=$dbname";
	$attr = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);

	$pdo = new PDO($dsn, $username, $password, $attr);

	
	if($pdo){
		if(!empty($_POST))
		{
			$_POST = null;
			$user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT );
			$post = filter_input(INPUT_POST, 'post', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW );
			
			$statement = $pdo->prepare("insert into post (date, user_id,post) values (NOW(), :user_id, :post)");
			$statement->bindparam(":user_id", $user_id);
			$statement->bindparam(":post", $post);
			$statement->execute();
		}
		?>
		<form action="index.php" method="post">
		<p>
			<label for="user_id">user:</label>
			<select name="user_id">
				<?php
					foreach($pdo->query("select * from users order by name") as $row)
					echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
					
				?>
			</select>
		</p>
		<p>
			<label for="post">Post:</label>
			<input type="text" name="post"/>
		</p>
			<input type="submit" value="post"/>
		</form>
		<hr/>
		<?php
		echo "<ul>";
		echo "<li><a href=\"index.php\">all users</a></li>";
		foreach ($pdo->query("select * from users order by name") as $row){
			echo "<li><a href=\"?user_id={$row['id']}\">{$row['name']}</a></li>";
		}
		echo "</ul>";
		echo "<hr />";
		if(!empty($_GET))
		{
			$_GET = null;
			$user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
			$statement = $pdo->prepare("SELECT post.*,users.name FROM post JOIN users ON users.id=post.user_id WHERE user_id=:user_id ORDER BY date");
			$statement->bindParam(":user_id", $user_id);
			if($statement->execute())
			{
				while($row = $statement->fetch())
				{
					echo "<p>{$row['date']} by {$row['name']} <br />
					{$row['post']}</p>";
				}
			}
			else
			{
				print_r($statement->errorInfo());
			}
			
		} 
		else{
			foreach ($pdo ->query("select post.*,users.name as user_name from post join users on users.id=post.user_id order by date") as $row){
			echo "<p>{$row['date']} by {$row['user_name']}<br/> {$row['post']}</p>";
		}
		}
	} 
	else{
		echo "not connected";
	}
?>
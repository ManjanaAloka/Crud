<?php
error_reporting(0);
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydatabase";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

    $firstname = $_REQUEST['fname'];
    $lastname = $_REQUEST['lname'];    
    $email = $_REQUEST['email'];  
    $pword = sha1($_REQUEST['pword']);

    $mode= $_REQUEST['mode'];
    $id= $_REQUEST['id'];


    $errors= array();
    $file_name = $_FILES['image']['name'];
    $file_size = $_FILES['image']['size'];
    $file_tmp = $_FILES['image']['tmp_name'];
    $file_type = $_FILES['image']['type'];  // abc.png, pqr.jpeg
    
   
    $file_ext=strtolower(end(explode('.',$_FILES['image']['name'])));
    $extensions= array("jpeg","jpg","png");
    
    $imagename='';


if($mode == 'add')
{   

   
  if(in_array($file_ext,$extensions)=== false)
  {
     $errors[]="extension not allowed, please choose a JPEG or PNG file.";
  }
  if($file_size > 2097152) 
  {
     $errors[]='File size must be excately 2 MB';
  }



    if(empty($errors)==true) 
    {
       move_uploaded_file($file_tmp,"images/".$file_name);
       echo "Success";

       $sql ="INSERT INTO myguests (firstname, lastname, email,`password`,imagename)
       values ('$firstname','$lastname','$email','$pword','$file_name')";
       $imagename=$file_name;
       if ($conn->query($sql) === TRUE) {
           echo "New record created successfully";
         } else {
           echo "Error: " . $sql . "<br>" . $conn->error;
         }
  
    }else{
       print_r($errors);
    }


  
}
if($mode == 'delete')
{
  //this code .... 
  $qrydel ="UPDATE myguests SET `status`=0 WHERE id=".$id;
  $resdel = $conn->query($qrydel);
  $file_name = $conn->query("SELECT imagename FROM myguests WHERE id='$id'");
  $fetch_file_name = $file_name->fetch_array();
  $folder = 'images/'.$fetch_file_name;
  echo $fetch_file_name;
  
  $files = glob($folder);
  foreach($files as $file){
    if(is_file($file)){
        unlink($file);
    }
  }


  if($resdel)
  {
    header("location:form1.php");
  }
  
  
  //$qrydel ="DELETE FROM myguests WHERE id=".$id;
  // $resdel = $conn->query($qrydel);

}

if($mode == 'edit')
{
//this code .... 
    $id = $_REQUEST['id'];
    $qryedit ="select id,firstname,lastname,email,imagename FROM myguests where status=1 and id=".$id;  
    $resedit = $conn->query($qryedit); 
    $rowedit = $resedit->fetch_array();
    $firstname = $rowedit['firstname'];
    $lastname = $rowedit['lastname'];
    $email = $rowedit['email'];
    $imagename = $rowedit['imagename'];
}


if($mode == 'update')
{
  
  if(in_array($file_ext,$extensions)=== false)
  {
     $errors[]="extension not allowed, please choose a JPEG or PNG file.";
  }
  if($file_size > 2097152) 
  {
     $errors[]='File size must be excately 2 MB';
  }

  if(empty($errors)==true) 
  {
    
     move_uploaded_file($file_tmp,"images/".$file_name);
     echo "Success";
  }
  //this code .... 
  $id = $_REQUEST['id'];
  echo $qryup ="UPDATE myguests SET firstname='$firstname',lastname ='$lastname',email='$email',imagename='$file_name' WHERE id=".$id;
  $resup = $conn->query($qryup);

}

$qry ="select id,firstname,lastname,email,imagename from myguests where status=1";
$res = $conn->query($qry);
?>

<form action="?mode=<?php echo($mode=='edit'?'update':'add');?>" method="post"
 enctype="multipart/form-data">

<input type="hidden" id="id" name="id" value="<?php echo $_GET['id'] ?>"><br>

  <label for="fname">First name:</label><br>
  <input type="text" id="fname" name="fname" value="<?php echo $firstname; ?>"><br>
  <label for="lname">Last name:</label><br>
  <input type="text" id="lname" name="lname" value="<?php echo $lastname; ?>">    <br>
  <label for="email">Email:</label><br>
  <input type="text" id="email" name="email" value="<?php echo $email; ?>"><br><br>
<?php if($mode != 'edit' && $mode != 'update' ) { ?> 
  <label for="password">Password:</label><br>
  <input type="password" id="pword" name="pword"><br><br>
  
  <?php } ?>
  <input type = "file" name = "image" /><br><br>

<img style="width:150px;height:150px" src="images/<?php echo $imagename; ?>">

<input type="submit" name="send" value="<?php echo($mode=='edit'?'Update':'Submit');?>">



</form>

<table border="1">
<tr>
  <td>First name</td>
  <td>Last name</td>
  <td>Email</td>
  <td>Image</td>
  <td>Action</td>
</tr>
<?php
  while($row = $res->fetch_assoc())
   {
?>
<tr>
  
  <td><?php echo $row['firstname']; ?></td>
  <td><?php echo $row['lastname']; ?></td>
  <td><?php echo $row['email']; ?></td>
  <td><?php if($row['imagename']) { ?><img style="width:150px;height:150px" src="images/<?php echo $row['imagename']; ?>"> <?php } else { echo "N/A";} ?></td>
  <td><a href="?id=<?php echo $row['id']; ?>&mode=edit"> Edit </a> | <a OnClick="return confirm('Are you sure you want to delete this record?');"    href="?id=<?php echo $row['id']; ?>&mode=delete"> Delete </a></td>
</tr>
    <?php  } ?>


</table>
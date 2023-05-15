
<!-- how to use
    1. Set the database connection details (host, username, password, and database name) in the functions.php script
    2. Import the PHP file to your project.
        example: include"functions.php";
    3. Use the provided functions to perform CRUD operations and image upload as needed.
-->

<!-- example -->
<?php
    // CREATE data
    $data = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
    ];
    $result = createData('member', $data); //createData($table, $data)

    // CREATE data with image
    $data = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
    ];
    $result = createDataWithImage('member', $data, 'photos', 'pict'); //createDataWithImage($table, $data, $image_database_name, $image_input_name)
?>


<?php
    // READ data
    $reads = readData('member');
    foreach($reads as $read){ ?>
        <tr>
            <td><?= $read['name'] ?></td>
            <td><?= $read['email'] ?></td>
        </tr>
<?php } ?>

<?php
    // READ data one
    $reads = readDataOne('member', "id_member= '$id' "); // readDataOne($table, $condition)
?>
        <tr>
            <td><?= $read['name'] ?></td>
            <td><?= $read['email'] ?></td>
        </tr>


<?php
    // UPDATE data
    $data = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
    ];
    $result = updateData('member', $data);

    // UPDATE data with image
    $data = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
    ];
    $result = updateDataWithImage('member', $data, "id_member='$id'", "photos", "pict" ) //updateDataWithImage($table, $data, $condition, $image_database_name ,$image_input_name)
?>

<?php
    // DELETE data
    $result = deleteData('product_tbl', "id_member='$id'"); //deleteData($table, $condition)

    // if data have an image
    $result = deleteData('product_tbl', "id_member='$id'", 'images/', 'photos'); //deleteData($table, $condition, $upload_folder, $image_database_name)
?>

<?php
    // JOIN tables
    // joinTables([table, table2, ....],  [join_condition1, join_condition2, ....])
    // Example Join 2 table
    $result = joinTables(['product_tbl', 'category_tbl'], ["product_tbl.category = category_tbl.id_category"]);
    // Example Join 3 table
    $result = joinTables(['checkout_tbl', 'product_tbl', 'customer_tbl'], ['product_tbl.id_product = checkout_tbl.id_product', 'customer_tbl.id_customer = checkout_tbl.id_customer']);
?>

<!-- 
    notes:
    for createDataWithImage() and updateDataWithImage()
    default $upload_folder = "images/"
    default $max_size = 2097152
    default $allowed_formats = ['jpg', 'png', 'gif', 'jpeg'].
    * you can modify it according to your needs. Edit the parameter $upload_folder = "....", $max_size = ...., $allowed_formats = ['jpg', '...']
 -->
 <!-- github.com/rizmulya/php-mysql-crud -->
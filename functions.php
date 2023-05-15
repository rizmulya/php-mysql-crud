<?php
// CONNET DB
$host = "localhost"; // hostname
$user = "root"; // username
$pass = ""; // password
$db = "..."; // database name

// connect to db
$conn = new mysqli($host, $user, $pass, $db);

// check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// CREATE data
function createData($table, $data) {
    global $conn;
    // retrieve the columns and values from the data to be added
    $columns = implode(",", array_keys($data)); // "a,b,c"
    $values = "'" . implode("','", array_values($data)) . "'"; // "'x','y','z'"

    // Creating SQL query
    $query = "INSERT INTO $table ($columns) VALUES ($values)";

    // Executing SQL query
    if ($conn->query($query) === TRUE) {
        return true;
    } else {
        return false;
    }
}

function createDataWithImage($table, $data, $image_database_name, $image_input_name, $upload_folder = "images/", $max_size = 2097152, $allowed_formats = ['jpg', 'png', 'gif', 'jpeg']) {
    global $conn;
    // filtering image input
    $file_name = $_FILES[$image_input_name]['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $new_file_name = uniqid() . '.' . $file_ext;
    $file_size = $_FILES[$image_input_name]['size'];
    $file_tmp_name = $_FILES[$image_input_name]['tmp_name'];

    if (in_array($file_ext, $allowed_formats)) {
        if ($file_size < $max_size) {
            // upload image
            move_uploaded_file($file_tmp_name, $upload_folder . $new_file_name);
            // create data
            $data[$image_database_name] = $new_file_name;
            $create = createData($table, $data);
            if($create){return true;}
        } else {
            echo "Failed! Maximum file size is 2mb.";
        }
    } else {
        echo "Failed! Only allowed formats are jpg, jpeg, gif, png.";
    }
}


// READ data
function readData($table, $condition = "") {
    global $conn;
    // Creating SQL query
    $query = "SELECT * FROM $table";
    if ($condition != "") {
        $query .= " WHERE $condition";
    }

    // Executing SQL query
    $result = $conn->query($query);

    // fetching the result
    $data = [];
    $i = 1; // sequence number
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $row['no'] = $i++; // sequence number
            $data[] = $row;
        }
    }

    return $data;
}

function readDataOne($table, $condition) {
    global $conn;
    // Creating SQL query
    $sql = "SELECT * FROM $table WHERE $condition";

    // Executing SQL query
    $result = $conn->query($sql);

    // fetching the result
    if ($result) {
        return $result->fetch_array();
    } else {
        return false;
    }
}


// UPDATE data
function updateData($table, $data, $condition) {
    global $conn;

    // Creating SQL query
    $query = "UPDATE $table SET ";
    foreach($data as $key => $value) {
        $query .= "$key='$value',";
    }
    $query = rtrim($query, ","); // Removing the last comma
    $query .= " WHERE $condition";

    // Executing SQL query
    if ($conn->query($query) === TRUE) {
        return true;
    } else {
        return false;
    }
}


function updateDataWithImage($table, $data, $condition, $image_database_name ,$image_input_name, $upload_folder = "images/", $max_size = 2097152, $allowed_formats = ['jpg', 'png', 'gif', 'jpeg'] ) {
    global $conn;
    // Filtering image
    $file_name = $_FILES[$image_input_name]['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $new_file_name = uniqid() . '.' . $file_ext;
    $file_size = $_FILES[$image_input_name]['size'];
    $file_tmp_name = $_FILES[$image_input_name]['tmp_name'];

    if(!$file_tmp_name=="") {
        if (in_array($file_ext, $allowed_formats)) {
            if ($file_size < $max_size) {
                // Upload file
                move_uploaded_file($file_tmp_name, $upload_folder . $new_file_name);
                // delete old image
                $data_file = readDataOne($table, $condition);
                unlink($upload_folder.$data_file[$image_database_name]);
                // Update data
                $data[$image_input_name] = $new_file_name;
                $result = updateData($table, $data, $condition);
                if($result){return true;}
            } else {
                echo "Failed! Maximum file size is 2mb.";
            }
        } else {
            echo "Failed! Only allowed formats are jpg, jpeg, gif, png.";
        }

    } else {
        $result = updateData($table, $data, $condition);
        if($result){return true;}
    }
}


// DELETE data
function deleteData($table, $condition, $upload_folder = false, $image_database_name = false) {
    global $conn;
    // Creating SQL query
    $query = "DELETE FROM $table WHERE $condition";

    // if data have an images
    if($upload_folder && $image_database_name != false){
        $image = readDataOne($table, $condition);
        unlink($upload_folder.$image[$image_database_name]);
    }

    // Executing SQL query
    if ($conn->query($query) === TRUE) {
        return true;
    } else {
        return false;
    }
}


// JOIN table
function joinTables($tables, $join_conditions, $select_columns = "*", $order_by = "", $limit = "") {
    global $conn;
    // Creating SQL query
    $sql = "SELECT $select_columns FROM $tables[0]";
    for ($i = 1; $i < count($tables); $i++) {
        $sql .= " INNER JOIN {$tables[$i]} ON {$join_conditions[$i-1]}";
    }
    if (!empty($order_by)) {
        $sql .= " ORDER BY $order_by";
    }
    if (!empty($limit)) {
        $sql .= " LIMIT $limit";
    }
    // Executing SQL query
    $result = $conn->query($sql);
    // Fetching the result
    if ($result->num_rows > 0) {
        $i = 1;
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $row['no'] = $i++;
            $data[] = $row;
        }
        return $data;
    } else {
        return false;
    }
}
// github.com/rizmulya/php-mysql-crud
?>
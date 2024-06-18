# JSON Encode
 
PHP JSON Encode large data with lesser resources
 

## Examples
 

### Generating single row output as object.
 

    <?php
    require "JsonEncode.php";
    
    // Create JsonEncode Object.
    $jsonEncodeObj = new JsonEncode();
    
    // Execute DB Query
    $stmt = $db->select($sql);
    $stmt->execute($params);
    
    // For single row - one
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $jsonEncode->encode($row);
    
    // For single row - two
    $jsonEncode->startObject();
    foreach($stmt->fetch(PDO::FETCH_ASSOC) as $key => $value) {
        $jsonEncode->addKeyValue($key, $value);
    }
    $jsonEncode->endObject();
    
    // Free statement resources and close the cursor.
    $stmt->closeCursor();
    
    $jsonEncode = null;

### Generating many rows output as array of objects.
 

    <?php
    require "JsonEncode.php";
    
    // Create JsonEncode Object.
    $jsonEncodeObj = new JsonEncode();
    
    // Execute DB Query
    $stmt = $db->select($sql);
    $stmt->execute($params);
    
    // For multiple rows
    $jsonEncode->startArray();
    for(;$row=$stmt->fetch(PDO::FETCH_ASSOC);) {
        $jsonEncode->encode($row);
    }
    $jsonEncode->endArray();
    
    // Free statement resources and close the cursor.
    $stmt->closeCursor();
    
    $jsonEncode = null;

### Generating many rows output inside object.
 

    <?php
    require "JsonEncode.php";
    
    // Create JsonEncode Object.
    $jsonEncodeObj = new JsonEncode();
    
    // Start JSON object
    $jsonEncode->startObject();
    
    // Execute DB Query - 1
    $stmt = $db->select($sql);
    $stmt->execute($params);
    foreach($stmt->fetch(PDO::FETCH_ASSOC) as $key => $value) {
        $jsonEncode->addKeyValue($key, $value);
    }
    // Free statement resources and close the cursor.
    $stmt->closeCursor();
    
    // Start JSON array inside object
    $jsonEncode->startArray($assocKey);
    
    // Execute DB Query - 2
    $stmt = $db->select($sql_2);
    $stmt->execute($params_2);
    for(;$row=$stmt->fetch(PDO::FETCH_ASSOC);) {
        $jsonEncode->encode($row);
    }
    // Free statement resources and close the cursor.
    $stmt->closeCursor();
    
    // End JSON array inside object
    $jsonEncode->endArray();
    
    // End JSON object
    $jsonEncode->endObject();
    
    $jsonEncode = null;

> The $jsonEncode = null; will stream the generated JSON.
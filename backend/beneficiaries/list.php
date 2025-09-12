<?php
$data = [
    "success" => true,
    "beneficiaries" => [],
    "pagination" => [
        "current_page" => 1,
        "total_pages" => 5,
        "total_records" => 50,
        "records_count" => 10
    ]
];
echo json_encode($data);
?>
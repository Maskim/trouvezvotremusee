<?php
    $dir      = dirname(__FILE__) . '/../../../uploads' ;//$_SERVER['DOCUMENT_ROOT']  ;
    $filename = 'bookings_export.csv';

    header('Expires: 0');
    header('Cache-control: private');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Description: File Transfer');
    header('Content-Type: application/csv');
    header('Content-disposition: attachment; filename='.$filename);
    readfile("$dir/$filename");
?>

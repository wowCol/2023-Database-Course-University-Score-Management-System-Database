<?php
$con = mysqli_connect("localhost","root", "");
mysqli_query($con, "set names 'utf8'");
if(! $con) {
    die("Wrong in connect into base database!");
}

mysqli_select_db($con, "202102_wqy_mis");

$_POST['semester'] = '大一';
$_POST['Sno'] = 'S01';
$_POST['Cno'] = 'C01';
//
//echo $_POST['Sno'];
//echo $_POST['Cno'];

if($_POST['semester'] == "全学年") {
    $sql = "SELECT wqy_course.Cno,wqy_course.Cname,wqy_course.OpeningSemester, averagescore.AverageScore
FROM wqy_course,averagescore
WHERE averagescore.Cno = wqy_course.Cno
ORDER BY wqy_course.Cno;";
} else {
    $sql = "SELECT wqy_course.Cno,wqy_course.Cname,wqy_course.OpeningSemester, averagescore.AverageScore
FROM wqy_course,averagescore
WHERE averagescore.Cno = wqy_course.Cno
AND wqy_course.OpeningSemester = '{$_POST['semester']}'
ORDER BY wqy_course.Cno;";
}

$retval = mysqli_query($con, $sql);

$data = "{";
$row = mysqli_fetch_array($retval);
for ($i = 0 ; $row; $i++) {
//        var_dump($row);
    $data .= "\"Cno{$i}\":\"{$row['Cno']}\",
    \"Cname{$i}\":\"{$row['Cname']}\",
    \"Semester{$i}\":\"{$row['OpeningSemester']}\",
    \"AverageScore{$i}\":\"{$row['AverageScore']}\",
    ";
    $row = mysqli_fetch_array($retval);
}

$data = rtrim($data);
$data = substr($data, 0, -1);
$data .= "}";
    echo $data;
//echo json_encode($data);
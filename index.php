<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<body>
<head>
<?

$url = 'http://softhouseeducation.se/wp-json';
$endpoint = '/wp/v2/pages?parent=16223';
$wpjson = $url.''.$endpoint;
$data = file_get_contents($wpjson);
$json = json_decode($data, true);

$manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
$manager->executeCommand('SHEVENT', new \MongoDB\Driver\Command(["drop" => "seminars"]));

/*$manager=new MongoDB\Driver\Manager();
$bulk1 = new MongoDB\Driver\BulkWrite;

$result = $manager->executeBulkWrite('SHEVENT.seminars', $bulk1);*/

function getBetween($content,$start,$end){
    $r = explode($start, $content);
    if (isset($r[1])){
        $r = explode($end, $r[1]);
        return $r[0];
    }
    return '';
}

function replacequote($event){
    $event = str_replace(array('\'', '"'), '', $event);
    return $event;
}
function replacecolon($event){
    $event = str_replace(array('\'', ':'), '', $event);
    $event = str_replace(array('\'', ';'), '', $event); 
    return $event;
}
function geteventname($title){
    $title = replacequote($title);
    return $title;
}
function geteventpic($event){
    $event = replacequote($event);
    $starteventpicture = "href=";
    $endeventpicture = "><img";
    $eventpicture = getBetween($event,$starteventpicture,$endeventpicture);
    return $eventpicture;
}
function geteventdesc($event){
    $event = strip_tags($event, '<h3>');
    //$event = replacequote($event);
    $starteventdesc = " Om Ämnet";
    $endeventdesc = "Om Föreläsaren";
    $eventdesc = getBetween($event,$starteventdesc,$endeventdesc);
    $startdesc1 = "<b>";
    $enddesc1 = "</b>";
    $eventdesc1 = getBetween($eventdesc,$startdesc1,$enddesc1);
    return $eventdesc;
}
function geteventspeakerinfo($event){
    $event = replacequote($event);
    $event = strip_tags($event, '<h3><img><a><p><b>');
    $starteventspeakerinfo = "</h3>\n<p>";
    $endeventspeakerinfo = "</p>";
    $eventspeakerinfo = getBetween($event,$starteventspeakerinfo,$endeventspeakerinfo);
    return $eventspeakerinfo;
}
function geteventspeakername($event){
    $event = replacequote($event);
    $starteventspeakername = "widgettitle>";
    $endeventspeakername = "</h3>\n<p>";
    $eventspeakername = getBetween($event,$starteventspeakername,$endeventspeakername);
    return $eventspeakername;
}
function geteventcontactpic($event){
    //$event = strip_tags($event, '<h3><img><a><p><b>');
    $event = replacequote($event);
    $startspeakpic = "<img src=";
    $endspeakpic = "class=jsjr-pci-photo";
    $eventspeakpic = getBetween($event,$startspeakpic,$endspeakpic);
    return $eventspeakpic;
}
function geteventcontactemail($event){
    $event = strip_tags($event, '<h3><img><a><p><b><h2><div>');
    $event = replacequote($event);
    $startspeakeremail = "jsjr-pci-email";
    $endspeakeremail = "jsjr-pci-phone";
    $eventspeakeremail = getBetween($event,$startspeakeremail,$endspeakeremail);
    $startspeakeremail1 = ">";
    $endspeakeremail1 = "<";
    $eventspeakeremail1 = getBetween($eventspeakeremail,$startspeakeremail1,$endspeakeremail1);
    return $eventspeakeremail1;
}
function geteventcontactphone($event){
    $event = strip_tags($event, '<h3><img><a><p><b><h2><div>');
    $startspeakerphone = "jsjr-pci-phone";
    $endspeakerphone = "/n";
    $eventspeakerphone = getBetween($event,$startspeakerphone,$endspeakerphone);
    $startspeakerphone1 = ">";
    $endspeakerphone1 = "<";
    $eventspeakerphone = getBetween($eventspeakerphone,$startspeakerphone1,$endspeakerphone1);
    return $eventspeakerphone;
}
function getcontactperson($event){
    $startcontact = "jsjr-pci-name";
    $endcontact = "jsjr-pci-email";
    $eventcontact = getBetween($event,$startcontact,$endcontact);
    $startcontact1 = ">";
    $endcontact1 = "<";
    $eventcontact = getBetween($eventcontact,$startcontact1,$endcontact1);
    return $eventcontact;

}
function geteventprice($event){
    $event = strip_tags($event, '<b>');
    $event = replacequote($event);
    $event = replacecolon($event);
    $startprice = "Pris";
    $endprice = "Språk";
    $eventprice = getBetween($event,$startprice,$endprice);
    $startprice1 = ">";
    $endprice1 = "<";
    $eventprice = getBetween($eventprice,$startprice1,$endprice1);
    return $eventprice;
}
function geteventlang($event){
    $event = replacequote($event);
    $event = replacecolon($event);
    $startlang = "Språk";
    $endlang = "DATUM";
    $eventlang = getBetween($event,$startlang,$endlang);
    $startlang1 = ">";
    $endlang1 = "<";
    $eventlang = getBetween($eventlang,$startlang1,$endlang1);
    return $eventlang;
}


function geteventplacedate($event){
    $event = strip_tags($event, '<h3><img><a><p><b><h2><div>');
    $event = replacequote($event);
    $event = replacecolon($event);
    $startplacedate = "DATUM";
    $endplacedate = "/p";
    $eventplacedate = getBetween($event,$startplacedate,$endplacedate);
    return getdates($eventplacedate);
        
}


function geteventplace($event){
    $event = strip_tags($event, '<h3><img><a><p><b><h2><div>');
    $event = replacequote($event);
    $event = replacecolon($event);
    $startplacedate = "DATUM";
    $endplacedate = "/p";
    $eventplacedate = getBetween($event,$startplacedate,$endplacedate);
    if (strpos($eventplacedate, 'Växjö') !== false) {
        return 'Växjö';
        
    }
    if (strpos($eventplacedate, 'Helsingborg') !== false) {
        return 'Helsingborg';
        
    }
    if (strpos($eventplacedate, 'Malmö') !== false) {
        return 'Malmö';
    }
    if (strpos($eventplacedate, 'Lund') !== false) {
        return 'Lund';
    }



}
function getViewdates($eventplacedate){
    $pattern='~\d+\W+\w\w\w\s\d\d\d\d~';
    $success = preg_match($pattern, $eventplacedate, $match);
    $date2 = $match[0];
    return $date2;

}
function getdates($date2){
    
    $date2=getViewdates($date2);
    if (strpos($date2, 'jan') !== false) {
        $date1 = str_replace('jan', '01',$date2);
        $pattern='~\W+~';
        $replacement = '-';
        $date1 = preg_replace($pattern, $replacement, $date1);
        
    }
     if (strpos($date2, 'feb') !== false) {
        $date1 = str_replace('feb', '02',$date2);
        $pattern='~\W+~';
        $replacement = '-';
        $date1 = preg_replace($pattern, $replacement, $date1);
    }
     if (strpos($date2, 'mar') !== false) {
        $date1 = str_replace('mar', '03',$date2);
        $pattern='~\W+~';
        $replacement = '-';
        $date1 = preg_replace($pattern, $replacement, $date1);
    }
     if (strpos($date2, 'apr') !== false) {
        $date1 = str_replace('apr', '04',$date2);
        $pattern='~\W+~';
        $replacement = '-';
        $date1 = preg_replace($pattern, $replacement, $date1);
    }
     if (strpos($date2, 'maj') !== false) {
        $date1 = str_replace('maj', '05',$date2);
        $pattern='~\W+~';
        $replacement = '-';
        $date1 = preg_replace($pattern, $replacement, $date1);
    }
     if (strpos($date2, 'jun') !== false) {
        $date1 = str_replace('jun', '06',$date2);
        $pattern='~\W+~';
        $replacement = '-';
        $date1 = preg_replace($pattern, $replacement, $date1);
    }
     if (strpos($date2, 'jul') !== false) {
        $date1 = str_replace('jul', '07',$date2);
        $pattern='~\W+~';
        $replacement = '-';
        $date1 = preg_replace($pattern, $replacement, $date1);
    }
     if (strpos($date2, 'aug') !== false) {
        $date1 = str_replace('aug', '08',$date2);
        $pattern='~\W+~';
        $replacement = '-';
        $date1 = preg_replace($pattern, $replacement, $date1);
    }
     if (strpos($date2, 'sep') !== false) {
        $date1 = str_replace('sep', '09',$date2);
        $pattern='~\W+~';
        $replacement = '-';
        $date1 = preg_replace($pattern, $replacement, $date1);
    }
     if (strpos($date2, 'okt') !== false) {
        $date1 = str_replace('okt', '10',$date2);
        //$date1 = str_replace('', '-',$date1);
        $pattern='~\W+~';
        $replacement = '-';
        $date1 = preg_replace($pattern, $replacement, $date1);

    }
     if (strpos($date2, 'nov') !== false) {
        $date1 = str_replace('nov', '11',$date2);
        $pattern='~\W+~';
        $replacement = '-';
        $date1 = preg_replace($pattern, $replacement, $date1);
    }
     if (strpos($date2, 'dec') !== false) {
        $date1 = str_replace('dec', '12',$date2);
        $pattern='~\W+~';
        $replacement = '-';
        $date1 = preg_replace($pattern, $replacement, $date1);
    }
    $date1 = date("Y-m-d", strtotime($date1));
    //$date1 = strtotime($date1);
    //$newformat = date('Y-m-d',$date1);
    return $date1.'T23:59:59';
}

/*function geteventmaterial($event){
    $event = replacestring($event);
    $starteventmaterial = "Material";
    $endeventmaterial = "Anmälan";
    $eventmaterial = getBetween($event,$starteventmaterial,$endeventmaterial);
    return $eventmaterial;
}*/ 
/*Metod för att hämta material som behövs till kursen, om det är något man vill 
implementera senare release*/

foreach ($json as &$value) {  
    $event = ($value[content]);
    $event = ($event[rendered]);
    $title = ($value[title]);
    $title = ($title[rendered]);
    $eventid = ($value[id]);
    $title = preg_replace("/#?[a-z0-9]+;/i","",$title); 
    $event = preg_replace("/#?[a-z0-9]+;/i","",$event);
    $event = str_replace(array('\'', '&'), ' ', $event); 
    //$title = mb_convert_encoding($title, 'ISO-8859-1', 'HTML-ENTITIES');   
    $connection=new MongoDB\Driver\Manager();
    $bulk = new MongoDB\Driver\BulkWrite;
    $doc = array(
    "eventID"=>$eventid,
    "title" => geteventname($title),
    "imageUrl" => geteventpic($event),
    "description" => geteventdesc($event),
    "speakerDescription" => geteventspeakerinfo($event),
    "speakerFullName" => geteventspeakername($event),
    "contactImageUrl" => geteventcontactpic($event),
    "contactEmail" => geteventcontactemail($event),
    "contactPhone" => geteventcontactphone($event),
    "contactPerson" => getcontactperson($event),
    "price" => geteventprice($event),
    "language" => geteventlang($event), 
    "location" => geteventplace($event),
    "numericDate" => getdates($event),
    "date"=> getViewDates($event)
    );
    $bulk->insert($doc);
    $connection->executeBulkWrite('SHEVENT.seminars', $bulk);
}
?>
</head>
</body>
</html>
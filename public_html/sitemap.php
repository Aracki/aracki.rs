<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/connect.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/date.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/utils.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/xml_tools.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/code/lib.php");

	$strSQL = "select Stra_Id, concat('".utils_getServerAddress()."', Stra_Link) as loc, 'daily' as changefreq, 1.0 as priority from Stranica ";
	$strToday = datetime_format4Database();
	$strSQL .= " where Stra_Valid=1 ";
	$strSQL .= " and ((Stra_PublishDate <= '".$strToday."' and Stra_ExpiryDate >= '".$strToday."')";
	$strSQL .= " or (Stra_PublishDate <= '".$strToday."' and Stra_ExpiryDate is null)";
	$strSQL .= " or (Stra_PublishDate is null and '".$strToday."' <= Stra_ExpiryDate)";
	$strSQL .= " or (Stra_PublishDate is null and Stra_ExpiryDate is null))";

	$xmlDoc = xml_loadXML(lib_getXmlMenu());

	$cats = con_getResults($strSQL);

    $site_map_container =& new google_sitemap(); 

    for ( $i=0; $i < count( $cats ); $i++ ){ 
        $value = $cats[ $i ]; 

		$stra_node = xml_getFirstElementByTagName($xmlDoc, "stranica_".$value[ 'Stra_Id' ]);
		
		$priority = 0.7;
		if ($value[ 'Stra_Id' ] == 1) $priority = 1.0;
		else if (!is_null($stra_node))
			$priority = getPriority(xml_getAttribute($stra_node, "dubina"));

        $site_map_item =& new google_sitemap_item( $value[ 'loc' ], "", $value[ 'changefreq' ], "" . $priority ); 

        $site_map_container->add_item( $site_map_item ); 
    } 

    header( "Content-type: application/xml; charset=\"".$site_map_container->charset . "\"", true ); 
    header( 'Pragma: no-cache' ); 
//	header("Content-Disposition: attachment; filename=\"sitemap.php\";" ); 


    print $site_map_container->build(); 


/** A class for generating simple google sitemaps
 *@author Svetoslav Marinov <svetoslav.marinov@gmail.com>
 *@copyright 2005
 *@version 0.1
 *@access public
 *@package google_sitemap
 *@link http://devquickref.com
 */
class google_sitemap
{
  var $header = "<\x3Fxml version=\"1.0\" encoding=\"UTF-8\"\x3F>\n\t<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\">";
  var $charset = "UTF-8";
  var $footer = "\t</urlset>\n";
  var $items = array();

  /** Adds a new item to the channel contents.
   *@param google_sitemap item $new_item
   *@access public
   */
  function add_item($new_item){
    //Make sure $new_item is an 'google_sitemap item' object
    if(!is_a($new_item, "google_sitemap_item")){
      //Stop execution with an error message
      trigger_error("Can't add a non-google_sitemap_item object to the sitemap items array");
    }
    $this->items[] = $new_item;
  }

  /** Generates the sitemap XML data based on object properties.
   *@param string $file_name ( optional ) if file name is supplied the XML data is saved in it otherwise returned as a string.
   *@access public
   *@return [void|string]
   */
  function build( $file_name = null )
  {
    $map = $this->header . "\n";

    foreach($this->items as $item)
    {
		$item->loc = htmlentities($item->loc, ENT_QUOTES);
      $map .= "\t\t<url>\n\t\t\t<loc>$item->loc</loc>\n";

	  // lastmod
      if ( !empty( $item->lastmod ) )
      	$map .= "\t\t\t<lastmod>$item->lastmod</lastmod>\n";

	  // changefreq
      if ( !empty( $item->changefreq ) )
      	$map .= "\t\t\t<changefreq>$item->changefreq</changefreq>\n";

	  // priority
      if ( !empty( $item->priority ) )
      	$map .= "\t\t\t<priority>$item->priority</priority>\n";

      $map .= "\t\t</url>\n\n";
    }

    $map .= $this->footer . "\n";

    if(!is_null($file_name)){
      $fh = fopen($file_name, 'w');
      fwrite($fh, $map);
      fclose($fh);
    }else{
      return $map;
    }
  }

}

/** A class for storing google_sitemap items and will be added to google_sitemap objects.
 *@author Svetoslav Marinov <svetoslav.marinov@gmail.com>
 *@copyright 2005
 *@access public
 *@package google_sitemap_item
 *@link http://devquickref.com
 *@version 0.1
*/
class google_sitemap_item
{
  /** Assigns constructor parameters to their corresponding object properties.
   *@access public
   *@param string $loc location
   *@param string $lastmod date (optional) format in YYYY-MM-DD or in "ISO 8601" format
   *@param string $changefreq (optional)( always,hourly,daily,weekly,monthly,yearly,never )
   *@param string $priority (optional) current link's priority ( 0.0-1.0 )
   */
  function google_sitemap_item( $loc, $lastmod = '', $changefreq = '', $priority = '' )
  {
    $this->loc = $loc;
    $this->lastmod = $lastmod;
    $this->changefreq = $changefreq;
    $this->priority = $priority;
  }
}

function getPriority($dubina){
	if ($dubina <= 2){
		return 0.9;
	} else return 0.9 - (($dubina-1)*0.1);
}

?>

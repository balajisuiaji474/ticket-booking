<?php		
	include('header.php');
	include ('includes/dbconn.php');	
	$conn = oci_connect($dbUserName, $dbPassword, $db);
	if (!$conn) {
		$e = oci_error();
		trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
	}
?>
<h1>Movie Shows...</h1>
<div id= "movieList">
<?php
	function searchForMovieId($id, $array) {
	   foreach ($array as $key => $val) {
		   if ($val['MOVIEID'] === $id) {			   
			   return $key;
		   }
	   }
	   return null;
	}
		
	
	$theatreSearchContent = $_POST['theatre-search-content'];
	function getMoviesBasedOnZipOrTheatreName($searchText,$conn) {
		$inside = "false";
		$searchQuery = "select * from theatres where upper(theatrename) like '%".strtoupper($searchText)."%'";
		if(is_numeric($searchText)){
			$searchQuery = "select * from theatres where zip = ".$searchText;			
		}						
		$theatreids = oci_parse($conn, $searchQuery);
		oci_execute($theatreids);
		while (($row = oci_fetch_array($theatreids, OCI_BOTH)) != false) {		
			$movieshows = oci_parse($conn, "select showid,movieid,screenid,theatreid,to_char(starttime, 'yyyy-mm-dd hh24:mi:ss') as starttime, price from movieshow where starttime >=sysdate and theatreid = ".$row['THEATREID']."order by starttime");
			$uniqueMovies = oci_parse($conn, "select movieid,moviename,rating from movies where movieid in (select unique movieid from movieshow where THEATREID =".$row['THEATREID'].")");
			oci_execute($uniqueMovies);
			oci_fetch_all($uniqueMovies, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC);
			oci_execute($movieshows);			
			while (($movieshowRow = oci_fetch_array($movieshows, OCI_BOTH)) != false) {		
				$inside="true";
				$starttime = date_format(date_create($movieshowRow['STARTTIME']), 'Y-m-d H:i:s');				
				$movieid = searchForMovieId($movieshowRow['MOVIEID'],$result);				
				echo "<br>";
				echo "<div class='movie'>";
				echo "<form action='/ticket-booking/ticket-selection.php' method='post'>";
				echo "<a class='review' href= '/ticket-booking/view-reviews.php?type=theatre&theatreid=".$movieshowRow['THEATREID']."'>View Review</a>";
				if (isset($_SESSION['username'])){		
					echo "&nbsp;<a  class='review' href='/ticket-booking/reviews.php?type=theatre&theatreid=".$movieshowRow['THEATREID']."'>Write Review</a>";
				}
				echo "<br>";
				echo "<span class='title'>Theatre: </span><span class='titleValue'>".$row['THEATRENAME']."</span>";
				echo "<br>";
				echo "<span class='title'>Movie: </span><span class='titleValue'>".$result[$movieid]['MOVIENAME']."</span><span class='rating'>&nbsp;&nbsp;&nbsp;<img src='includes/likes.png'/ title='Users Rating'>".number_format( ($result[$movieid]['RATING'] / 10) * 100, 0)."%</span>";
				echo "<br>";
				echo "<span class='title'>Show Start Time: </span><span class='titleValue'>".$movieshowRow['STARTTIME']."</span>";				
				echo "<span class='movieOrder'><input type='submit' value='Select'></span>";
				echo "<input type=hidden name='showid' value=\"".$movieshowRow['SHOWID']."\">";
				echo "<input type=hidden name='theatreid' value=\"".$movieshowRow['THEATREID']."\">";
				echo "<input type=hidden name='movieid' value=\"".$movieshowRow['MOVIEID']."\">";
				echo "<input type=hidden name='screenid' value=\"".$movieshowRow['SCREENID']."\">";	
				echo "<input type=hidden name='theatrename' value=\"".$row['THEATRENAME']."\">";
				echo "<input type=hidden name='moviename' value=\"".$result[$movieid]['MOVIENAME']."\">";				
				echo "<input type=hidden name='moviestarttime' value=\"".$movieshowRow['STARTTIME']."\">";				
				echo "<input type=hidden name='ticketprice' value=\"".$movieshowRow['PRICE']."\">";				
				echo "<input type=hidden name='movierating' value=\"".number_format( ($result[$movieid]['RATING'] / 10) * 100, 0)."\">";	
				echo "</form>";
				echo "</div>";
			}
		}		
		return $inside;
	}
	
	function getMoviesBasedOnMovieName($searchText,$conn) {
		$inside = "false";
		$searchQuery = "select * from movies where upper(moviename) like '%".strtoupper($searchText)."%'";		
		$movieMatches = oci_parse($conn, $searchQuery);
		oci_execute($movieMatches);
		while (($row = oci_fetch_array($movieMatches, OCI_BOTH)) != false) {		
			$movieshows = oci_parse($conn, "select showid,movieid,screenid,theatreid,to_char(starttime, 'yyyy-mm-dd hh24:mi:ss') as starttime, price from movieshow where starttime >=sysdate and movieid = ".$row['MOVIEID']."order by starttime");			
			oci_execute($movieshows);			
			while (($movieshowRow = oci_fetch_array($movieshows, OCI_BOTH)) != false) {	
				$inside= "true";
				$starttime = date_format(date_create($movieshowRow['STARTTIME']), 'Y-m-d H:i:s');
				$theatreDetails = oci_parse($conn, "select theatrename from theatres where theatreid=".$movieshowRow['THEATREID']);
				oci_define_by_name($theatreDetails, 'THEATRENAME', $theatrename);				
				oci_execute($theatreDetails);				
				echo "<br>";
				echo "<div class='movie'>";
				echo "<form action='/ticket-booking/ticket-selection.php' method='post'>";
				echo "<a class='review' href= '/ticket-booking/view-reviews.php?type=theatre&theatreid=".$movieshowRow['THEATREID']."'>View Review</a>";
				if (isset($_SESSION['username'])){		
					echo "&nbsp;<a  class='review' href='/ticket-booking/reviews.php?type=theatre&theatreid=".$movieshowRow['THEATREID']."'>Write Review</a>";
				}
				echo "<br>";
				while (oci_fetch($theatreDetails)) {
					echo "<span class='title'>Theatre: </span><span class='titleValue'>".$theatrename."</span>";
					echo "<input type=hidden name='theatrename' value=\"".$theatrename."\">";
				}				
				echo "<br>";
				echo "<span class='title'>Movie: </span><span class='titleValue'>".$row['MOVIENAME']."</span><span class='rating'>&nbsp;&nbsp;&nbsp;<img src='includes/likes.png'/ title='Users Rating'>".number_format( ($row['RATING'] / 10) * 100, 0)."%</span>";
				echo "<br>";
				echo "<span class='title'>Show Start Time: </span><span class='titleValue'>".$movieshowRow['STARTTIME']."</span>";				
				echo "<span class='movieOrder'><input type='submit' value='Select'></span>";
				echo "<input type=hidden name='showid' value=\"".$movieshowRow['SHOWID']."\">";
				echo "<input type=hidden name='theatreid' value=\"".$movieshowRow['THEATREID']."\">";
				echo "<input type=hidden name='movieid' value=\"".$movieshowRow['MOVIEID']."\">";
				echo "<input type=hidden name='screenid' value=\"".$movieshowRow['SCREENID']."\">";							
				echo "<input type=hidden name='moviename' value=\"".$row['MOVIENAME']."\">";			
				echo "<input type=hidden name='moviestarttime' value=\"".$movieshowRow['STARTTIME']."\">";		
				echo "<input type=hidden name='ticketprice' value=\"".$movieshowRow['PRICE']."\">";				
				echo "<input type=hidden name='movierating' value=\"".number_format( ($row['RATING'] / 10) * 100, 0)."\">";					
				echo "</form>";
				echo "</div>";
			}
		}
		
		return $inside;
	}
	$count = 0;
	$output = getMoviesBasedOnZipOrTheatreName($theatreSearchContent,$conn);
	if($output=="true") {$count +=1;}
	$splitSearchText= explode(" ", $theatreSearchContent);	
	if(count($splitSearchText) > 1 ) {
		foreach($splitSearchText as $key => $val) {
			$output=getMoviesBasedOnZipOrTheatreName($splitSearchText[$key], $conn);
			if($output=="true") {$count +=1;}
		}
	}	
	if(!empty($theatreSearchContent)) {
	
		$output=getMoviesBasedOnMovieName($theatreSearchContent,$conn);
		if($output=="true") {$count +=1;}	
	}	
	if ($count==0) {
		echo "<span style='color:red;'>Movie Shows Not Available</span>";
	}	
?>
</div>
<?php
	include('footer.php');
	oci_close($conn);
?>
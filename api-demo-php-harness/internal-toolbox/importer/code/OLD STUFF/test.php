<?php
$input = fopen('../../../internal-toolbox/importer/data.CC.csv', 'r');  //open for reading
$output = fopen('../../../internal-toolbox/importer/temporary.csv', 'w'); //open for writing
while( false !== ( $data = fgetcsv($input) ) ){  //read each line as an array

   //modify data here
   if ($data[11] !== '0') {
      //Replace line here
      $data[11] = substr($data[11],-4);
      //echo("SUCCESS|Password changed!");
   }

   //write modified data to new file
   fputcsv( $output, $data);
}

//close both files
fclose( $input );
fclose( $output );

//clean up
unlink('../../../internal-toolbox/importer/data.CC.csv');// Delete obsolete BD
rename('../../../internal-toolbox/importer/temporary.csv', '../../../internal-toolbox/importer/new.csv'); //Rename temporary to new

?>
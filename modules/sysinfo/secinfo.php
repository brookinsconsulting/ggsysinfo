<?php
/**
 * Tests security of eZ Publish install. Based on phpsecinfo tests
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2008-2012
 * @license Licensed under GNU General Public License v2.0. See file license.txt
*/

$psi = new PhpSecInfo();
$psi->loadAndRun();
$results = $psi->getResultsAsArray();

// suppress unwanted results
//unset( $results['test_results']['Core']['memory_limit'] );
//unset( $results['test_results']['Core']['post_max_size'] );
//unset( $results['test_results']['Core']['upload_max_filesize'] );

$tpl->setVariable( 'results', $results );

?>
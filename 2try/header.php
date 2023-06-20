<?php
$python_output = exec("python get_domains.py");

$domains = json_decode($python_output, true);

include "global_variable.php";

?>
<nav>
    <div class="logo"> <a href="./index.php"> <img src="./images/books.png" alt="Image Description"></a></div>
    <div class="dropdown">
        <button class="dropbtn">Content</button>
        <div class="dropdown-content">
            <?php
            if ($domains[0] != null) {
                for($i = 0; $i < count($domains); $i++) {
            echo '
        <a href="domain_template.php?domain_name='.$domains[$i].'"">'.$domains[$i].'</a> ';
                }}
        ?>
        </div>
    </div>
    
    <div class="dropdown">
    <button class="dropbtn">Clusters</button>
    <div class="dropdown-content">
        <?php
       function generateNestedDropdown($cluster) {
        $keys = array_keys((array)$cluster);
        if ($keys[0] != null) {
            foreach ($keys as $key) {
                $clusterContent = $cluster->{$key};
                $clusterKeywords = $clusterContent->keywords;
                
                echo '<div class="nested-dropdown">
                        <a href="cluster_template.php?cluster='.$key.'">'.$clusterKeywords[0].'</a> 
                        <div class="nested-dropdown-content">';
                
                // if (!empty($clusterKeywords)) {
                //     foreach ($clusterKeywords as $keyword) {
                //         echo '<a href="#">'.$keyword.'</a>';
                //     }
                // }
                
                // Check if there are nested clusters within this cluster
                $nestedClusters = (object)array_diff_key((array)$clusterContent, ['filenames' => null, 'keywords' => null]);
                
                if (!empty((array)$nestedClusters)) {
                    generateNestedDropdown($nestedClusters);
                }
                
                echo '</div>
                    </div>';
            }
        }
    }
        
        // Assuming $cluster is the main cluster object containing all clusters
        generateNestedDropdown($cluster2);
        ?>
    </div>
</div>


    <ul>
         <!-- search bar right align -->
         <li>
         <div class="search">
            <form action="search.php" method="get">
                <input type="text"
                    placeholder=" Search Articles"
                    name="search">
                <button>
                    <i class="fa fa-search"
                        style="font-size: 18px;">
                    </i>
                </button>
            </form>
        </div></li>
        <li><a href="shelf.php">Shelf</a></li>
        <li><a href="logout.php">LogOut</a></li>
        <li><a href="lda_visualization.html">Topic Modeling</a></li>

    </ul>
 
</nav>

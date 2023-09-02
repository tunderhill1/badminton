<?php   class nucleoGraph {
    var $graph_width;        #generated automatically
    var $graph_height;        #static for now
    var $chunk_width;        #the width of the grey region in which two bars exist
    var $max1;                #max height of the first graph
    var $max2;                #duh
    var $usable_graph_height;    #just graph height minus 2
    var $name;                #the id of the graph for CSS purposes (don't have two on one page w/ same name)
    var $fade;                #whether or not to have the PNG fade image, not <=IE6 friendly
    var $ticks;                #The number of background rows (ticks)
    var $tick_height;
    var $data = Array();    #contains each bar's data (label, value1, value2)
    var $color = Array();    #contains each bar's color (face, light, dark) [1 or 2]

    # I don't know default constructors in PHP4, if they even exist
    function construct($chunk, $max1, $max2, $name = "nucleoGraph", $fade = false, $num_ticks = 5, $tick_height = 60) {
        $this->chunk_width = $chunk;
        $this->max1 = $max1;
        $this->max2 = $max2;
        $this->name = $name;
        $this->fade = $fade;
        $this->ticks = $num_ticks;
        $this->tick_height = $tick_height - 1;
        $this->color[1] = array('face' => '666', 'light' => 'fff', 'dark' => '000');
        $this->color[2] = array('face' => '666', 'light' => 'fff', 'dark' => '000');
        $this->graph_height = $num_ticks * $tick_height;
        $this->usable_graph_height = $num_ticks * $tick_height - 2;
    }
    function setColors($bar, $face, $light, $dark) {
        $this->color[$bar] = array('face' => $face, 'light' => $light, 'dark' => $dark);
    }
    function addBar($label, $value1, $value2 = 0) {
        $this->data[] = array('label' => $label, 'value1' => $value1, 'value2' => $value2);
        $this->graph_width = sizeof($this->data) * $this->chunk_width;
    }
    function getCount() {
        return sizeof($this->data);
    }
    function clearData() {
        $this->data = array();
    }
    function displayDebug() {    #it is a beta after all (-:
        echo "<pre>";
        print_r($this->data);
        print_r($this->color);
        echo "graph_width => " . $this->graph_width . "\n";
        echo "graph_height => " . $this->graph_height . "\n";
        echo "chunk_width => " . $this->chunk_width . "\n";
        echo "max1 => " . $this->max1 . "\n";
        echo "max2 => " . $this->max2 . "\n";
        echo "usable_graph_height => " . $this->usable_graph_height . "\n";
        echo "name => " . $this->name . "\n";
        echo "</pre>\n";
    }
    function displayGraph($recalculateMaxHeights = FALSE) {
    $number = sizeof($this->data);
    if ($this->fade){
        $bg = " url(fade-dark.png) repeat-x;";
    } else {
        $bg = '';
    }

    if ($recalculateMaxHeights) {
        $max_height = 0;
        foreach($this->data AS $data) {
            if ($data['value1'] > $max_height)
                $max_height = $data['value1'];
            if ($data['value2'] > $max_height)
                $max_height = $data['value2'];
        }
        $this->max1 = ceil($max_height / 100) * 100;
        $this->max2 = $this->max1;
    }

?>

<style type="text/css">
#<?php  =$this->name?> {position: relative; width: 97%; height: <?php  =$this->graph_height?>px;
  margin: 10px 0 20px 20px; padding: 0;
  background: #DDD;
  border: 4px solid #7599C0; list-style: none;
  font: 5px Helvetica, Geneva, sans-serif;}
#<?php  =$this->name?> {margin: 0; padding: 0; list-style: none;}
#<?php  =$this->name?> li {position: absolute; bottom: 0; width: <?php  =100/$number?>%; z-index: 2;
  margin: 0; padding: 0;
  text-align: center; list-style: none;}
#<?php  =$this->name?> li.qtr {height: <?php  =$this->usable_graph_height?>px; padding-top: 2px;
  border-right: 1px dotted #C4C4C4; color: #AAA;}
#<?php  =$this->name?> li.bar {width: 45%; border: 1px solid; border-bottom: none; color: #000;}
#<?php  =$this->name?> li.bar p {margin: -5px 0 0; padding: 0;}
#<?php  =$this->name?> li.sent {left: 25%; background: #<?php  =$this->color[1]['face']?><?php  =$bg?>; border-color: #<?php  =$this->color[1]['light']?> #<?php  =$this->color[1]['dark']?> #000 #<?php  =$this->color[1]['light']?>;}
#<?php  =$this->name?> li.paid {left: 50%; background: #<?php  =$this->color[2]['face']?><?php  =$bg?>; border-color: #<?php  =$this->color[2]['light']?> #<?php  =$this->color[2]['dark']?> #000 #<?php  =$this->color[2]['light']?>;}
<?php   for ($i=1; $i <= $number; $i++) {
    ?>#<?php  =$this->name?> #q<?php  =$i?> {left: <?php  =( (100/$number)*($i-1) ) ?>%; <?php   if ($i == $number) { ?>border-right: none;<?php   } ?> }
<?php   }?>

#<?php  =$this->name?> #ticks {width: 100%; height: <?php  =$this->graph_height?>px; z-index: 1;}
#<?php  =$this->name?> #ticks .tick {position: relative; border-bottom: 1px solid #BBB; width: 100%;}
#<?php  =$this->name?> #ticks .tick p {position: absolute; left: 100%; top: -0.67em; margin: 0 0 0 0.5em;}
</style>
<ul id="<?php  =$this->name?>">
<?php   for ($i=0; $i < sizeof($this->data); $i++) {

    #this will be put into an array in a later version for variable bar group size support
    $height = ceil(($this->data[$i]['value1'] / $this->max1) * $this->usable_graph_height);
    $height2 = ceil(($this->data[$i]['value2'] / $this->max2) * $this->usable_graph_height);
    if ($height > $this->usable_graph_height)
        $height = $this->usable_graph_height;
    if ($height2 > $this->usable_graph_height)
        $height2 = $this->usable_graph_height;

?><li class="qtr" id="q<?php  =$i+1?>"><?php  =$this->data[$i]['label']?>
<ul>
<li class="sent bar" style="height: <?php  =$height?>px;"><p><?php  =$this->data[$i]['value1']?></p></li>
<?php   if ($this->data[$i]['value2'] > 1) { ?>
<li class="paid bar" style="height: <?php  =$height2?>px;"><p><?php  =$this->data[$i]['value2']?></p></li>
<?php   } ?>
</ul>
</li>
<?php   }
?>
<li id="ticks">
<?php   for($j = $this->ticks; $j >= 1; $j--) {
    $value = ceil(($j/$this->ticks)*$this->max1);
?>
<div class="tick" style="height: <?php  =$this->tick_height?>px;"><p><?php  =$value?></p></div>
<?php   }
?>
</li>
</ul>
<?php   }
}

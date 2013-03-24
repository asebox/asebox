<?php

/**
* Asemon Theme class
*/
class AsemonTheme extends Theme 
{
    protected $font_color       = '#0044CC';
//    protected $background_color = '#DDFFFF';
    protected $background_color = '#FAEDA9';
    protected $axis_color       = '#0066CC';
    protected $grid_color       = '#3366CC';

    function GetColorList() {
        return array(
/* vivid */        
            '#005EBC',
            '#FF4A26',
            '#FFFB11',
            '#9AEB67',
            '#FDFF98',
            '#6B7EFF',
            '#BCE02E',
            '#E0642E',
            '#E0D62E',
            '#2E97E0',
            '#02927F',
            '#FF005A',
/* pastel

            '#AACCFF',
            '#FFAACC',
            '#AAEECC',
            '#CCAAFF',
            '#EEDDFF',
            '#FFCCAA',
            '#CCBBDD',
            '#CCFFAA',
            '#C7D7C2',
            '#FFEEDD',
            '#FFCCEE',
            '#BFECFA',
*/
        );
    }

    function SetupGraph($graph) {

        // graph
        /*
        $img = $graph->img;
        $height = $img->height;
        $graph->SetMargin($img->left_margin, $img->right_margin, $img->top_margin, $height * 0.25);
        */
        $graph->SetFrame(true,'#3E7882');
        $graph->SetMarginColor('white');
        //$graph->SetBackgroundGradient($this->background_color, '#FFFFFF', GRAD_HOR, BGRAD_PLOT);
        //$graph->SetBackgroundGradient('AntiqueWhite3', 'AntiqueWhite1', GRAD_HOR, BGRAD_MARGIN); 
        $graph->SetBackgroundGradient($this->background_color, '#FFFFFF', GRAD_HOR, BGRAD_MARGIN);


        // legend
        $graph->legend->SetFrameWeight(0);
        $graph->legend->Pos(0.5, 0.85, 'center', 'top');
        $graph->legend->SetFillColor('white');
        $graph->legend->SetLayout(LEGEND_HOR);
        $graph->legend->SetColumns(3);
        $graph->legend->SetMarkAbsSize(5);
        $graph->legend->SetShadow('black', 2);

        // xaxis
        $graph->xaxis->SetFont(FF_COURIER,FS_NORMAL,8);
        $graph->xaxis->title->SetColor($this->font_color);  
        //$graph->xaxis->SetColor($this->axis_color, $this->font_color);    
        $graph->xaxis->SetColor('#3E7882', 'black');
        $graph->xaxis->SetTickSide(SIDE_BOTTOM);
        $graph->xaxis->SetLabelMargin(3);
        $graph->xaxis->HideLine(false);
        $graph->xaxis->HideTicks(false, false);
        $graph->xaxis->SetWeight(2);
        $graph->xaxis->SetLabelAngle(45);
                
        // yaxis
        $graph->yaxis->SetFont(FF_COURIER,FS_NORMAL,8);
        $graph->yaxis->title->SetColor($this->font_color);  
        //$graph->yaxis->SetColor($this->axis_color, $this->font_color);    
        $graph->yaxis->SetColor('#3E7882', 'black');
        $graph->yaxis->SetTickSide(SIDE_LEFT);
        $graph->yaxis->SetLabelMargin(8);
        $graph->yaxis->HideLine(false);
        $graph->yaxis->HideTicks(false, false);
        $graph->xaxis->SetTitleMargin(15);
        $graph->yaxis->SetWeight(2);

        // grid
        $graph->ygrid->SetColor($this->grid_color);
        $graph->ygrid->SetLineStyle('dotted');


        // font
        $graph->title->SetColor($this->font_color);
        $graph->subtitle->SetColor($this->font_color);
        $graph->subsubtitle->SetColor($this->font_color);

//        $graph->img->SetAntiAliasing();
    }


    function SetupPieGraph($graph) {

        // graph
        $graph->SetFrame(true,'#3E7882');

        // legend
        //$graph->legend->SetFillColor('white');
        //$graph->legend->SetFrameWeight(0);
        //$graph->legend->Pos(0.5, 0.80, 'center', 'top');
        //$graph->legend->SetLayout(LEGEND_HOR);
        //$graph->legend->SetColumns(4);
        //$graph->legend->SetShadow(false);
        //$graph->legend->SetMarkAbsSize(5);
        $graph->legend->SetShadow('black', 2);

        // title
        $graph->title->SetColor($this->font_color);
        $graph->subtitle->SetColor($this->font_color);
        $graph->subsubtitle->SetColor($this->font_color);

        $graph->SetAntiAliasing();
        $graph->SetColor($this->background_color);
    }


    function PreStrokeApply($graph) {
        if ($graph->legend->HasItems()) {
            $img = $graph->img;
            $height = $img->height;
            $graph->SetMargin(
                $img->raw_left_margin, 
                $img->raw_right_margin, 
                $img->raw_top_margin, 
                $height * 0.25
            );
        }
    }

    function ApplyPlot($plot) {

        switch (get_class($plot))
        { 
            case 'GroupBarPlot':
            {
                foreach ($plot->plots as $_plot) {
                    $this->ApplyPlot($_plot);
                }
                break;
            }

            case 'AccBarPlot':
            {
                foreach ($plot->plots as $_plot) {
                    $this->ApplyPlot($_plot);
                }
                break;
            }

            case 'AccLinePlot':
            {
                foreach ($plot->plots as $_plot) {
//                    $this->ApplyPlot($_plot);
                    $color = $this->GetNextColor();
                    $_plot->SetColor($color);
                    //$plot->SetFillColor($color);
                    $_plot->SetFillGradient($color, 'white');
                }
                break;
            }

            case 'BarPlot':
            {
                $plot->Clear();

                $color = $this->GetNextColor();
                $plot->SetColor($color);
                //$plot->SetFillColor($color);
                $plot->SetFillGradient($color, 'white',GRAD_HOR);
                //$plot->SetShadow();
                break;
            }

            case 'LinePlot':
            {
                //$plot->Clear();
                if ($plot->color == "black") {
                    // This color was automaticaly generated (not specificly set by graph before calling the add method)
                    $color = $this->GetNextColor();
                    $plot->SetColor($color);
                    //$plot->SetFillColor($color);
                    $plot->SetFillGradient($color, 'white');
                }
                $plot->SetWeight(1);
//                $plot->SetBarCenter();
                break;
            }


            case 'PiePlot':
            {
                $plot->SetCenter(0.5, 0.45);
                $plot->SetSliceColors($this->GetThemeColors());
                $plot->SetColor('black');
                $plot->SetShadow();
                $plot->SetGuideLines();
                $plot->ShowBorder(true,true);
                break;
            }

            case 'PiePlot3D':
            {
                $plot->SetSliceColors($this->GetThemeColors());
                break;
            }
    
            default:
            {
            }
        }
    }
}


?>

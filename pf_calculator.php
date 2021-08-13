<?php
defined('_JEXEC') or die();
class pfCalculator
{
    public function __construct($project, $data)
    {
        // Чтобы использовать зависимые поля, переконвертируем многомерный массив с данными в одномерный. Заодно отфильтруем поля без math.
        $this->data = $this->linerData($data, true);
    }
    public function getPrice()
    {
        $opt = $this->getOptions();

        switch ($opt['type']) {
            case 'cards':
                return $this->cards($opt);

            case 'flyers':
                return $this->flyers($opt);

            case 'booklets':
                return $this->booklets($opt);

            case 'catalogues':
                return $this->catalogues($opt);

            case 'calendars':
                return $this->calendars($opt);

            case 'postcards':
                return $this->postcards($opt);

            case 'other':
                return $this->other($opt);
        }

        return 0;
    }
    protected function getOptions()
    {
        $opt = array();

        // коэффициенты
        // $opt['br'] = 3.710; // часовая стоимость работы Брошюровщика
        // $opt['des'] = 3.710; // часовая стоимость работы Дизайнера
        // $opt['con'] = 4.722; // часовая стоимость работы Печатник
        // $opt['fol'] = 4.73; // стоимость фольги
        // $opt['dev'] = 352; // стоимость материала Девелопер
        // $opt['kor'] = 97.5; // стоимость материала Коротрон
        // $opt['ton'] = 172.68; // стоимость материала Тонер
        // $opt['fot'] = 1149.42; // стоимость материала Фотобарабан
        // $opt['nal'] = 0.346; // налог
        // $opt['hoz'] = 4.02; // хоз расход
		// $opt['mim'] = 145;  // стоимость материала чернила Mimaki
		// $opt['hol'] = 13.92 // стоимость материала холст натуральный
		// $opt['skb'] = 10.00 // стоимость материала Скобы для строительного степлера
		// $opt['podr'] = 3.00 // стоимость материала Подрамник дерево (40х20мм)
		// $opt['nar'] = 4.72 // часовая стоимость работы Специалист наружной рекламы
		// $opt['shpech'] = 4.72 // часовая стоимость работы Печатник широкоформатной печати
		// $opt['plbel'] = 4.68 // стоимость плёнки белой матовой
        // $opt['plgl'] = 4.68 //  стоимость плёнки прозрачной глянцевой
	
        //  значения по умолчанию
        $opt['type'] = '';   // что считаем ( в math типа: type_cards )
        $opt['quantity'] = 0;   // тираж ( quantity_48 )
		$opt['paper'] = 0;   // бумага ( paper_0.2513_2 )
        $opt['thickness'] = 0;   // коэффициент плотности ( передаётся в бумаге )
        $opt['lamination'] = false;   // ламинация  ( lamination )
        $opt['lamT'] = 0;   // тип ламинации ( lamT_1.9942 )
        $opt['lamQty'] = 1;   // кол-во сторон ламинации ( lamQty_2 )
        $opt['rounding'] = false;   // скругление углов ( rounding )
        $opt['design'] = false;   // наличие макета ( design )
        $opt['printing'] = 0;   // кол-во сторон Полноцветной печати ( printing_2 )
        $opt['foiling'] = false;   // фольгирование ( foiling )
        $opt['foilingQty'] = 1;   // кол-во сторон фольгирования ( foilingQty_2 )
        $opt['holes'] = false;   // сверление отверстий ( holes )
		$opt['luvers']= false; // установка люверсов ( luvers )
		$opt['srochno']= false; // срочный заказ, умножаем итог на 1.3;
		$opt['qnt'] = 0;   // тираж Листовок
		$opt['formpaper'] = 0; // размер бумаги длина ширина formpaper_200_100
        $opt['dl'] = 0; // длина бумаги передаётся в $opt['formpaper']
		$opt['shr'] = 0; // ширина бумаги передаётся $opt['formpaper']
		$opt['big'] = 0; // количество изгибов буклет, передаётся в $opt['formpaper']
		$opt['dlvvod'] = 1; // длина бумаги вводимая пользователем
		$opt['shrvvod'] = 1; // ширина вводимая пользователем
		$opt['bigovka'] = 0; // биговка
		$opt['plenka'] = false; // выбор плёнки для печать (наклейки)
		$opt['laminakl'] = 0; // ламинация наклеек
		$opt['plrezka'] = 0; // выбор резки плотером наклеек
		$opt['rrezka'] = 0; // выбор резки руками
		$opt ['plrr'] = 0; // плотерная +ручная Вырезанные по контуру (выдается поштучно)
		$opt['vidrez'] = 0;   // коэффициент плотерной резки (вид плотерной резки) ( vidrez_1 )
		$opt['kalka'] = 0; // Вкладыш для открытки
		$opt['ushki'] = false; // ручная резка ушек

        foreach ($this->data as $field) {
            if (strpos($field->math, 'type_') === 0) {
                $parts = explode('_', $field->math);
                $opt['type'] = $parts[1];
            }
            elseif (strpos($field->math, 'vidrez_') === 0) {
                $parts = explode('_', $field->math);
                $opt['vidrez'] = (int) $parts[1];
            }
			elseif (strpos($field->math, 'quantity_') === 0) {
                $parts = explode('_', $field->math);
                $opt['quantity'] = (int) $parts[1];
            }
			elseif($field->math == 'dlvvod'){
                $opt['dlvvod'] = (int) $field->value;
              }
			elseif($field->math == 'shrvvod'){
                $opt['shrvvod'] = (int) $field->value;
              }
			elseif($field->math == 'qnt'){
                $opt['qnt'] = (int) $field->value;
              }
							 
            elseif (strpos($field->math, 'formpaper_') === 0) {
                $parts = explode('_', $field->math);
                $opt['dl'] = (int) $parts[1];
                $opt['shr'] = (int) $parts[2];
				$opt['big'] = (int) $parts[3];
            }
			elseif (strpos($field->math, 'paper_') === 0) {
                $parts = explode('_', $field->math);
                $opt['paper'] = (float) $parts[1];
                $opt['thickness'] = (float) $parts[2];
            }
            elseif($field->math == 'lamination') {
                $opt['lamination'] = true;
            }
            elseif (strpos($field->math, 'lamT_') === 0) {
                $parts = explode('_', $field->math);
                $opt['lamT'] = (float) $parts[1];
            }
            elseif (strpos($field->math, 'lamQty_') === 0) {
                $parts = explode('_', $field->math);
                $opt['lamQty'] = (int) $parts[1];
            }
            elseif($field->math == 'rounding') {
                $opt['rounding'] = true;
            }
            elseif($field->math == 'design') {
                $opt['design'] = true;
            }
            elseif (strpos($field->math, 'printing_') === 0) {
                $parts = explode('_', $field->math);
                $opt['printing'] = (int) $parts[1];
            }
            elseif($field->math == 'foiling') {
                $opt['foiling'] = true;
            }
            elseif (strpos($field->math, 'foilingQty_') === 0) {
                $parts = explode('_', $field->math);
                $opt['foilingQty'] = (int) $parts[1];
            }
            elseif($field->math == 'holes') {
                $opt['holes'] = true;
            }
			elseif($field->math == 'bigovka') {
                $opt['bigovka'] = true;
            }
			elseif($field->math == 'luvers') {
                $opt['luvers'] = true;
            }
			elseif($field->math == 'srochno') {
                $opt['srochno'] = true;
            }
			 elseif($field->math == 'plenka') {
                $opt['plenka'] = true;
            }
			elseif($field->math == 'laminakl') {
                $opt['laminakl'] = true;
            }
			elseif($field->math == 'plrezka') {
               $opt['plrezka'] = true;
           }
			elseif($field->math == 'rrezka') {
                $opt['rrezka'] = true;
            }
			elseif($field->math == 'plrr') {
                $opt ['plrr'] = true;
            }
			elseif($field->math == 'kalka') {
                $opt['kalka'] = true;
            }
			elseif($field->math == 'ushki') {
                $opt['ushki'] = true;
            }
            elseif (strpos($field->math, 'des') && strpos($field->math, 'hoz')) {
                $parts = explode(';', $field->math);
                foreach ($parts as $pat) {
                    $pa = explode('=', $pat);
                    $opt[trim($pa[0])] = trim($pa[1]);
                }
            }
        }

        return $opt;
    }

    protected function cards($opt) {
        $folg = 0;
        $zakugl = 0;
        $sverl = 0;
		$luv = 0;
		$ustluv = 0;
        $lam = 0;
        $lamT = 0;
        $lamF = 0;
        $sroch = 1;
        $KolStukNaListe = max(floor(450/94)*floor(320/54),floor(450/54)*floor(320/94));
        $kollist = round($opt['quantity']/$KolStukNaListe*1.03+0.5);
 
        if($opt['lamination']) {
            $lam = (0.05+0.00667*$kollist)*$opt['br'];
            $lamT = (0.45*0.32*$kollist*$opt['lamQty'])*$opt['lamT'];
        }

        if($opt['rounding']) {
            $zakugl = (0.05+0.00046*$opt['quantity'])*$opt['br'];
        }

        if($opt['design']) {
            $maket = 0.06*$opt['des'];
        } else {
            $maket = 0.5*$opt['des'];
        }

        if($opt['foiling']) {
            $lamF = (0.45*0.32*$kollist*$opt['foilingQty'])*$opt['fol'];
            $folg = (0.05+0.01344*$kollist)*$opt['br'];
        }

        if($opt['holes']) {
            $sverl = (0.05+0.0002*$opt['quantity'])*$opt['br'];
        }
		 if($opt['luvers']) {
            $luv = 0.02*$opt['quantity'];
			$ustluv = 0.002*$opt['quantity']*$opt['br'];
        }
		if($opt['srochno']) {
            $sroch = 1.3;
        }

        $gil = (0.05+0.00004*($opt['quantity']/4+$opt['quantity'])*350/80)*$opt['br'];
        $konica = (0.05+(1/1008)*$kollist*$opt['printing']*$opt['thickness'])*$opt['con'];
        $dev = ($kollist/45700*$opt['printing'])*$opt['dev'];
        $kor = (($kollist/18518)*$opt['printing'])*$opt['kor'];
        $ton = (($kollist/2700)*$opt['printing'])*$opt['ton'];
        $fot = (($kollist/37000)*$opt['printing'])*$opt['fot'];
		
        $vidrab = $maket + $gil + $konica + $lam + $folg + $zakugl + $sverl + $ustluv;

        $nalog=$vidrab*$opt['nal'];
        $hozras=$vidrab*$opt['hoz'];
        $prseb = $opt['paper']*$kollist + $vidrab + $dev + $kor + $ton + $fot + $lamT + $lamF + $nalog + $hozras + $luv;
        $pribul = $prseb * (60/100);
        $usn = ($pribul + $prseb) * (5/95);

        return ($pribul + $usn + $prseb)*$sroch;
    }

    protected function flyers($opt) {
		
		$sroch = 1;
		
		if($opt['design']) {
            $maket = 0*$opt['des'];
        } else {
            $maket = 1.5*$opt['des'];
        }
		if($opt['srochno']) {
            $sroch = 1.3;
        }
	
        $KolStukNaListeL = max(floor(437/$opt['dl'])*floor(300/$opt['shr']),floor(437/$opt['shr'])*floor(300/$opt['dl']));
        $kollistL = round($opt['qnt']/$KolStukNaListeL*1.03+0.5);
	    $gil = (0.05+0.00004*($opt['qnt']/4+$opt['qnt'])*350/80)*$opt['br'];
		$dev = ($kollistL/45700*$opt['printing'])*$opt['dev'];
        $kor = (($kollistL/18518)*$opt['printing'])*$opt['kor'];
        $ton = (($kollistL/2700)*$opt['printing'])*$opt['ton'];
        $fot = (($kollistL/37000)*$opt['printing'])*$opt['fot'];
		$konica = (0.05+(1/1008)*$kollistL*$opt['printing']*$opt['thickness'])*$opt['con'];
		
	 $vidrab = $maket + $gil + $konica;

        $nalog=$vidrab*$opt['nal'];
        $hozras=$vidrab*$opt['hoz'];
        $prseb = $opt['paper']*$kollistL + $vidrab + $dev + $kor + $ton + $fot + $nalog + $hozras;
        $pribul = $prseb * (60/100);
        $usn = ($pribul + $prseb) * (5/95);

        return ($pribul + $usn + $prseb)*$sroch;
    }

    protected function booklets($opt) {
		
		$folg = 0;
        $zakugl = 0;
        $sverl = 0;
		$luv = 0;
		$ustluv = 0;
        $lam = 0;
        $lamT = 0;
        $lamF = 0;
		$sroch = 1;
		$bigovka = 0;
		
		
		$KolStukNaListeL = max(floor(437/$opt['dl'])*floor(300/$opt['shr']),floor(437/$opt['shr'])*floor(300/$opt['dl']));
        $kollistL = round($opt['qnt']/$KolStukNaListeL*1.03+0.5);
		
		if($opt['lamination']) {
            $lam = (0.05+0.00667*$kollistL)*$opt['br'];
            $lamT = (0.45*0.32*$kollistL*$opt['lamQty'])*$opt['lamT'];
        }

        if($opt['rounding']) {
            $zakugl = (0.05+0.00046*$opt['qnt'])*$opt['br'];
        }

        if($opt['foiling']) {
            $lamF = (0.45*0.32*$kollistL*$opt['foilingQty'])*$opt['fol'];
            $folg = (0.05+0.01344*$kollistL)*$opt['br'];
        }

        if($opt['holes']) {
            $sverl = (0.05+0.0002*$opt['qnt'])*$opt['br'];
        }
		 if($opt['luvers']) {
            $luv = 0.02*$opt['qnt'];
			$ustluv = 0.002*$opt['qnt']*$opt['br'];
        }
		if($opt['design']) {
            $maket = 0*$opt['des'];
        } else {
            $maket = 1.5*$opt['des'];
        }
		if($opt['srochno']) {
            $sroch = 1.3;
        }
	    if($opt['bigovka']) {
            $bigovka = (0.05+0.0025*$opt['qnt'])*$opt['br'];
        }
        
	    $gil = (0.05+0.00004*($opt['qnt']/4+$opt['qnt'])*350/80)*$opt['br'];
		$dev = ($kollistL/45700*$opt['printing'])*$opt['dev'];
        $kor = (($kollistL/18518)*$opt['printing'])*$opt['kor'];
        $ton = (($kollistL/2700)*$opt['printing'])*$opt['ton'];
        $fot = (($kollistL/37000)*$opt['printing'])*$opt['fot'];
		$konica = (0.05+(1/1008)*$kollistL*$opt['printing']*$opt['thickness'])*$opt['con'];
		
	 $vidrab = $maket + $gil + $konica + $lam + $folg + $zakugl + $sverl + $ustluv + $bigovka;

        $nalog=$vidrab*$opt['nal'];
        $hozras=$vidrab*$opt['hoz'];
        $prseb = $opt['paper']*$kollistL + $vidrab + $dev + $kor + $ton + $fot + $lamT + $lamF + $nalog + $hozras + $luv;
        $pribul = $prseb * (60/100);
        $usn = ($pribul + $prseb) * (5/95);

        return ($pribul + $usn + $prseb)*$sroch;
    }
/////////////////////////// ОТКРЫТКИ ///////////////////////////

    protected function catalogues($opt) {
		$folg = 0;
        $zakugl = 0;
        $sverl = 0;
		$luv = 0;
		$ustluv = 0;
        $lam = 0;
        $lamT = 0;
        $lamF = 0;
        $sroch = 1;
		$kalka = 0;
		$ushko = 0;
		$matkalka = 0;
		
        $KolStukNaListe = max(floor(437/($opt['dl']*$opt['dlvvod']))*floor(300/($opt['shr']*$opt['shrvvod'])),floor(437/($opt['shr']*$opt['shrvvod']))*floor(300/($opt['dl']*$opt['dlvvod'])));
        $kollist = round($opt['qnt']/$KolStukNaListe*1.03+0.5);
        $kolkalki = round(1/6*$opt['qnt']*1.03+0.5);
		
        if($opt['lamination']) {
            $lam = (0.05+0.00667*$kollist)*$opt['br'];
            $lamT = (0.45*0.32*$kollist*$opt['lamQty'])*$opt['lamT'];
        }

        if($opt['rounding']) {
            $zakugl = (0.05+0.00046*$opt['qnt'])*$opt['br'];
        }

        if($opt['design']) {
            $maket = 0*$opt['des'];
        } else {
            $maket = 1*$opt['des'];
        }

        if($opt['foiling']) {
            $lamF = (0.45*0.32*$kollist*$opt['foilingQty'])*$opt['fol'];
            $folg = (0.05+0.01344*$kollist)*$opt['br'];
        }

        if($opt['holes']) {
            $sverl = (0.05+0.0002*$opt['qnt'])*$opt['br'];
        }
		 if($opt['luvers']) {
            $luv = 0.02*$opt['qnt'];
			$ustluv = 0.002*$opt['qnt']*$opt['br'];
        }
		if($opt['srochno']) {
            $sroch = 1.3;
        }
		
///////////// калька //////////
		if($opt['kalka']) {
		$gilK = (0.05+0.00004*($opt['qnt']/4+$opt['qnt'])*350/80)*$opt['br'];
        $konicaK = (0.05+(1/1008)*$kolkalki)*$opt['con'];
        $devK = ($kolkalki/45700)*$opt['dev'];
        $korK = ($kolkalki/18518)*$opt['kor'];
        $tonK = ($kolkalki/2700)*$opt['ton'];
        $fotK = ($kolkalki/37000)*$opt['fot'];
		$stkalk = $kolkalki*0.37;
		
        }
		if($opt['ushki']) {
            $ushko = (0.0083*2*$opt['qnt'])*$opt['br'];
        }
		
        $kalka = $gilK + $konicaK + $ushko;
		$matkalka = $devK + $korK + $tonK + $fotK + $stkalk;
//////////// конец калька ////////

        $gil = (0.05+0.00004*($opt['qnt']/4+$opt['qnt'])*350/80)*$opt['br'];
        $konica = (0.05+(1/1008)*$kollist*$opt['printing']*$opt['thickness'])*$opt['con'];
        $dev = ($kollist/45700*$opt['printing'])*$opt['dev'];
        $kor = (($kollist/18518)*$opt['printing'])*$opt['kor'];
        $ton = (($kollist/2700)*$opt['printing'])*$opt['ton'];
        $fot = (($kollist/37000)*$opt['printing'])*$opt['fot'];

		$bigovka = ((0.05+0.0025*$opt['qnt'])*$opt['br'])*$opt['big'];
		$bumaga = $opt['paper']*$kollist; // бумага
		
        $vidrab = $maket + $gil + $konica + $lam + $folg + $zakugl + $sverl + $ustluv + $bigovka;
        $material = $bumaga + $dev + $kor + $ton + $fot + $lamT + $lamF + $nalog + $hozras + $luv;
		
        $nalog=($vidrab + $kalka)*$opt['nal'];
        $hozras=($vidrab + $kalka)*$opt['hoz'];
        $prseb = $vidrab + $material + $kalka + $matkalka + $nalog + $hozras;
        $pribul = $prseb * (60/100);
        $usn = ($pribul + $prseb) * (5/95);

        return ($pribul + $usn + $prseb)*$sroch;
    }
//////////////////////ФОТО НА ХОЛСТЕ//////////////////////////////////////////////////////////

    protected function calendars($opt) {
		
		$sroch = 1;
		
		if($opt['srochno']) {
            $sroch = 1.3;
        }
		
		$plholst = 0.137+($opt['shr']/1000)*($opt['dl']/1000)*$opt['qnt']*1.1;// Общая площадь холста
		//$razmp = ($opt['shr']/1+50)*($opt['dl']/1+50);   //Размер печатного изображения в мм
		//$razppod = $opt['shr']*$opt['dl'] ;   //Размер подрамника
		
		$mimaki = (10/1000*$plholst*2)*$opt['mim']; //чернила
		$holst = $plholst*$opt['hol']; //холст
		$skoba = ((1/1000*round(($opt['shr']/1000+$opt['dl']/1000)*2*20)+0.008)*$opt['qnt'])*$opt['skb']; // скобы
		$podram = ((($opt['dl']/1000)+($opt['shr']/1000))*2*$opt['qnt'])*$opt['podr']; // подрамник
		
		$shirforpech = (60/12*$plholst/60)*2*$opt['shpech']; // широкоформатная печать
		$shfp = ((($opt['dl']/1000)+($opt['shr']/1000))*2*0.67*$opt['qnt']/60)*$opt['nar']; // ШФП
		$natyaj = (0.25*$opt['qnt'])*$opt['nar']; // нятяжка на подрамник
		
        $vidrab = $shirforpech + $shfp + $natyaj;

        $nalog=$vidrab*$opt['nal'];
        $hozras=$vidrab*$opt['hoz'];
        $prseb = $vidrab + $nalog + $hozras + $mimaki + $holst + $skoba + $podram;
        $pribul = $prseb * (60/100);
        $usn = ($pribul + $prseb) * (5/95);
         return ($pribul + $usn + $prseb)*$sroch;
    }
	
//////////////////////НАКЛЕЙКИ//////////////////////////////////////////////////////////

    protected function postcards($opt) {
		
		$sroch = 1;
		$rukrez = 0;
		$plrez = 0;
		
		
		if($opt['srochno']) {
            $sroch = 1.3;
        }
		if($opt['design']) {
            $maket = 0;
        } else {
            $maket = 0.75*$opt['des'];
        }
			
		$spl = 0.126+($opt['shrvvod']/1000+0.002)*($opt['dlvvod']/1000+0.002)*$opt['qnt']*1.1; // Общая площадь пленки
		
// материалы //
 $mimaki = (10/1000*$spl)*$opt['mim']; //чернила

	if($opt['plenka']) {
            $matbel = $spl*$opt['plbel']; //Пленка белая матовая
        } else {
            $prgl = $spl*$opt['plgl'];  //Пленка прозрачная глянцевая
        }
	    if($opt['laminakl']) {
		    $prglam = $spl*$opt['plgl'];  //Пленка прозрачная глянцевая для ламинации
			$hollam = (60/12*$spl/6/60)*$opt['shpech']; //Холодное ламинирование
            }
	    if($opt['plrezka']) {
		$plrez = (0.05+($opt['vidrez']*$opt['qnt']*0.015)*$opt['vidrez']/60)*$opt['shpech'];  //Плоттерная резка    
		 } 
		 
		if($opt['rrezka']) {
   		$rukrez = ((($opt['dlvvod']/1000)+($opt['shrvvod']/1000))*2*0.67*$opt['qnt']/60)*$opt['nar']; //Ручная резка ШФП печати в размер
        }
		if($opt ['plrr']) {
   		$plrrsum = ((($opt['dlvvod']/1000)+($opt['shrvvod']/1000))*2*0.67*$opt['qnt']/60)*$opt['nar'] + (0.05+($opt['vidrez']*$opt['qnt']*0.015)*$opt['vidrez']/60)*$opt['shpech']; //Ручная резка + плотерная
        }
		
	    $spfn = (60/12*$spl/60)*$opt['shpech']; //Широкоформатная печать
								  
								 
		
        $vidrab = $spfn + $maket + $rukrez + $plrez + $hollam + $plrrsum; //виды работ
		$material = $mimaki + $prgl + $matbel +$prglam; // материалы

        $nalog=$vidrab*$opt['nal']; //налоги
        $hozras=$vidrab*$opt['hoz']; // хоз расходы
        $prseb = $vidrab + $nalog + $hozras + $material; // производственная себистоимость
        $pribul = $prseb * (60/100); // прибыль 
        $usn = ($pribul + $prseb) * (5/95); // налог при УСН
            return ($pribul + $usn + $prseb)*$sroch; // ИТОГО
    }
	
/////////////////////////// БУКЛЕТЫ ///////////////////////////

    protected function other($opt) {
		
		$sroch = 1;
		
		if($opt['design']) {
            $maket = 0*$opt['des'];
        } else {
            $maket = 1.5*$opt['des'];
        }
		if($opt['srochno']) {
            $sroch = 1.3;
        }
	
        $KolStukNaListeL = max(floor(437/$opt['dl'])*floor(300/$opt['shr']),floor(437/$opt['shr'])*floor(300/$opt['dl']));
        $kollistL = round($opt['qnt']/$KolStukNaListeL*1.03+0.5);
	    $gil = (0.05+0.00004*($opt['qnt']/4+$opt['qnt'])*350/80)*$opt['br'];
		$dev = ($kollistL/45700*2)*$opt['dev'];
        $kor = (($kollistL/18518)*2)*$opt['kor'];
        $ton = (($kollistL/2700)*2)*$opt['ton'];
        $fot = (($kollistL/37000)*2)*$opt['fot'];
		$konica = (0.05+(1/1008)*$kollistL*2*$opt['thickness'])*$opt['con'];
		$bigovka = (0.05+0.0025*$opt['qnt']*$opt['big'])*$opt['br'];
		
		
	 $vidrab = $maket + $gil + $konica + $bigovka;

        $nalog=$vidrab*$opt['nal'];
        $hozras=$vidrab*$opt['hoz'];
        $prseb = $opt['paper']*$kollistL + $vidrab + $dev + $kor + $ton + $fot + $nalog + $hozras;
        $pribul = $prseb * (60/100);
        $usn = ($pribul + $prseb) * (5/95);

        return ($pribul + $usn + $prseb)*$sroch;
    }


		
    //Вспомогательная функция

    protected function linerData($data, $mathOnly=false)
    {
        $newdata = array();

        foreach ($data as $field) {
            if ($field->teg == 'cloner' || $field->teg == 'qftabs') {
                foreach ($field->data as $row) {
                    $arr = $this->linerData($row, $mathOnly);
                    $newdata = array_merge($newdata, $arr);
                }
            } else {
                if ($mathOnly) {
                    if (isset($field->math) && $field->math !== '') {
                        $newdata[] = $field;
                    }
                } else {
                    $newdata[] = $field;
                }
                if (isset($field->data) && ! empty($field->data)) {
                    $arr = $this->linerData($field->data, $mathOnly);
                    $newdata = array_merge($newdata, $arr);
                }
            }
        }

        return $newdata;
    }
}




<?php
namespace TestBundle\EventListener;

use TestBundle\Event\TestEvent;
use PHPExcel;
use PHPExcel_IOFactory;

class TestEventListener
{
    public function createExport(TestEvent $event)
    {
        $this->phpExcelTest($event);
    }

    protected function phpExcelTest(TestEvent $event)
    {
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Hello')
            ->setCellValue('B2', 'world!')
            ->setCellValue('C1', 'Hello')
            ->setCellValue('D2', 'world!');

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A4', 'Miscellaneous glyphs')
            ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');


        $objPHPExcel->getActiveSheet()->setCellValue('A8',"Hello\nWorld");
        $objPHPExcel->getActiveSheet()->getRowDimension(8)->setRowHeight(-1);
        $objPHPExcel->getActiveSheet()->getStyle('A8')->getAlignment()->setWrapText(true);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($event->getData('rootDir') . '/../web/downloads/testExcelByListener.xlsx');
    }
}
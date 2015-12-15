<?php
namespace AppBundle\Service;

use PHPExcel;
use PHPExcel_IOFactory;

class XlsExport
{
    /**
     * @param $name
     * @param $head
     * @param $data
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function export($name, $head, $data)
    {
        $objPHPExcel = new PHPExcel();

        $rowIndex = 1;
        $columnIndex = 0;
        foreach ($head as $column) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($columnIndex, $rowIndex, $column);
            $columnIndex++;
        }
        $rowIndex++;
        foreach ($data as $row) {
            $columnIndex = 0;
            foreach ($row as $value) {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($columnIndex, $rowIndex, $value);
                $columnIndex++;
            }
            $rowIndex++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('downloads/' . $name . '.xlsx');
    }
}
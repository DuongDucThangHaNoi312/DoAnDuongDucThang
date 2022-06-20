<?php

namespace App\Providers;

Use \Maatwebsite\Excel\Sheet;
use \Maatwebsite\Excel\Writer;
use Illuminate\Support\ServiceProvider;

class PHPExcelMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Page format macros
         */
        Writer::macro('setCreator', function (Writer $writer, string $creator) {
            $writer->getDelegate()->getProperties()
            // ->setCreator("NIBE.,JSC")
            ->setLastModifiedBy("Jupiter")
            ->setTitle("Round Use")
            ->setSubject("Round Use")
            ->setDescription("Round Use")
            ->setKeywords("Round Use")
            ->setCompany("Jupiter Group")
            // ->setCategory("Test result file")
            ->setCreator($creator);
        });

        Sheet::macro('setOrientation', function (Sheet $sheet, $orientation) {
            $sheet->getDelegate()->getPageSetup()->setOrientation($orientation);
        });

        /**
         * Cell macros
         */
        Writer::macro('setCellValue', function (Writer $writer, string $cell, string $data) {
            $writer->getDelegate()->setCellValue($cell, $data);
        });

        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
        });
        Sheet::macro('horizontalAlign', function (Sheet $sheet, string $cellRange, string $align) {
            $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal($align);
        });

        Sheet::macro('verticalAlign', function (Sheet $sheet, string $cellRange, string $align) {
            $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setVertical($align);
        });

        Sheet::macro('wrapText', function (Sheet $sheet, string $cellRange) {
            $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);
        });

        Sheet::macro('mergeCells', function (Sheet $sheet, string $cellRange) {
            $sheet->getDelegate()->mergeCells($cellRange);
        });

        Sheet::macro('columnWidth', function (Sheet $sheet, string $column, float $width) {
            $sheet->getDelegate()->getColumnDimension($column)->setWidth($width);
        });
        Sheet::macro('rowHeight', function (Sheet $sheet, string $row, float $height) {
            $sheet->getDelegate()->getRowDimension($row)->setRowHeight($height);
        });

        Sheet::macro('allRowsHeight', function (Sheet $sheet, float $height) {
            $sheet->getDelegate()->getDefaultRowDimension()->setRowHeight($height);
        });

        Sheet::macro('setFontFamily', function (Sheet $sheet, string $cellRange, string $font) {
            $sheet->getDelegate()->getStyle($cellRange)->getFont()->setName($font);
        });

        Sheet::macro('setFontSize', function (Sheet $sheet, string $cellRange, float $size) {
            $sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize($size);
        });

        Sheet::macro('textRotation', function (Sheet $sheet, string $cellRange, int $degrees) {
            $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setTextRotation($degrees);
        });
    }
}

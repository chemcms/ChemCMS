<?php
// +----------------------------------------------------------------------
// | ChemCMS [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.chemcms.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Sam <sam_zb@chemcms.com>
// +----------------------------------------------------------------------
namespace app\chem\logic;

use think\Db;

class ChemProductLogic
{

    public function getImportFileRowsCount($file)
    {
        $phpExcelReaderExcel2007 = new \PHPExcel_Reader_Excel2007();
        $phpExcelObj             = $phpExcelReaderExcel2007->load($file);
        $currentSheet            = $phpExcelObj->getActiveSheet();
        $highestColumn           = $currentSheet->getHighestColumn();
        $allRow                  = $currentSheet->getHighestRow();

        return $allRow - 1;
    }

    public function getImportFileRows($file, $firstRow, $listRows)
    {
        vendor("PHPExcel.PHPExcel");
        \PHPExcel_Settings::setCacheStorageMethod(\PHPExcel_CachedObjectStorageFactory::cache_to_discISAM);
        $phpExcelReaderExcel2007 = new \PHPExcel_Reader_Excel2007();
        $phpExcelObj             = $phpExcelReaderExcel2007->load($file);
        $currentSheet            = $phpExcelObj->getActiveSheet();
        $highestColumn           = $currentSheet->getHighestColumn();
        $allRow                  = $currentSheet->getHighestRow();
        $allColumn               = \PHPExcel_Cell::columnIndexFromString($highestColumn);

        for ($currentColumnIndex = 0; $currentColumnIndex < $allColumn; $currentColumnIndex++) {
            $currentColumn = \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex);
            $address       = $currentColumn . "1";
            $currentCell   = $currentSheet->getCell($address);
            $value         = $currentCell->getValue();

            if (empty($value)) {
                $allColumn = $currentColumnIndex;
                break;
            }

        }

        $rows     = [];
        $firstRow = $firstRow + 2;
        $lastRow  = $firstRow + $listRows - 1;
        $lastRow  = $lastRow > $allRow ? $allRow : $lastRow;

        for ($currentRowIndex = $firstRow; $currentRowIndex <= $lastRow; $currentRowIndex++) {
            $row              = [];
            $row['row_index'] = $currentRowIndex;
            for ($currentColumnIndex = 0; $currentColumnIndex < $allColumn; $currentColumnIndex++) {
                $currentColumn = \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex);
                $address       = $currentColumn . $currentRowIndex;
                $currentCell   = $currentSheet->getCell($address);
                $value         = trim($currentCell->getValue());

                $key = $this->getColumnKey($currentColumnIndex, $currentSheet);
                if (!empty($key)) {
                    if ($key == 'specification') {
                        $row['pack'] = intval($value);
                        $row['unit'] = str_replace($row['pack'], '', $value);
                    } else {
                        $row[$key] = $value;
                    }
                }
            }
            $currentColumnIndex = 0;
            array_push($rows, $row);
        }

        return $rows;
    }

    public function importFileRows($file, $firstRow, $listRows)
    {
        $rows   = $this->getImportFileRows($file, $firstRow, $listRows);
        $logs   = [];
        $userId = cmf_get_current_admin_id();

        if (!empty($rows)) {
            foreach ($rows as $row) {
                $rowIndex = $row['row_index'];
                //1.写入化合物库数据
                $cas     = $row['cas'];
                $findCas = Db::name('chem_product')->field('id')->where(['cas' => $cas])->find();
                if ($findCas) {
                    $data = ['update_time' => time()];
                    empty($row['cn_name']) or $data['cn_name'] = $row['cn_name'];
                    empty($row['name']) or $data['name'] = $row['name'];
                    try {
                        Db::name('chem_product')->where(['cas' => $row['cas']])->update($data);
                    } catch (\Exception $e) {
                        array_push($logs, ['row_index' => $rowIndex, 'message' => '化合物数据写入失败']);
                        continue;
                    }
                    $productId = $findCas['id'];
                } else {
                    try {
                        $productId = Db::name('chem_product')->insertGetId([
                            'cas'         => $row['cas'],
                            'cn_name'     => $row['cn_name'],
                            'name'        => $row['name'],
                            'user_id'     => $userId,
                            'create_time' => time(),
                            'status'      => 1 //状态;1:发布;0 :下架
                        ]);
                    } catch (\Exception $e) {
                        array_push($logs, ['row_index' => $rowIndex, 'message' => '化合物数据写入失败']);
                        continue;
                    }
                }

                // 设置产品分类
                $findCategory = Db::name('chem_category')->field('id')->where(['name' => $row['category_name']])->find();
                if (empty($findCategory)) {
                    $findCategoryId = Db::name('chem_category')->insertGetId(['name' => $row['category_name']]);
                } else {
                    $findCategoryId = $findCategory['id'];
                }
                $findCategoryProductCount = Db::name('chem_category_product')->where(['category_id' => $findCategoryId, 'product_id' => $productId])->count();

                if ($findCategoryProductCount == 0) {
                    Db::name('chem_category_product')->insert([
                        'category_id' => $findCategoryId,
                        'product_id'  => $productId
                    ]);
                }

                // 添加品牌
                $findBrandCount = Db::name('chem_brand')->where(['name' => $row['brand']])->count();
                if ($findBrandCount == 0) {
                    Db::name('chem_brand')->insert([
                        'name' => $row['brand'],
                    ]);
                }

                //写入库存数据
                $findStoragePlace = Db::name('chem_storage_place')->field('id')->where(['name' => $row['storage_place']])->find();
                if (empty($findStoragePlace)) {
                    try {
                        $findStoragePlaceId = Db::name('chem_storage_place')->insertGetId(['name' => $row['storage_place']]);
                    } catch (\Exception $e) {
                        array_push($logs, ['row_index' => $rowIndex, 'message' => "试剂柜【{$row['storage_place']}】不存在,请添加后再上传" . Db::name('chem_storage_place')->getLastSql()]);
                        continue;
                    }
                } else {
                    $findStoragePlaceId = $findStoragePlace['id'];
                }


                $findProductPack = Db::name('chem_product_pack')->field('id')->where(['product_id' => $productId, 'pack' => $row['pack'], 'unit' => $row['unit'], 'product_no' => $row['product_no']])->count();

                try {
                    if ($findProductPack) {
                        Db::name('chem_product_pack')->where(['id' => $findProductPack])->update([
                            'price'            => floatval($row['price']),
                            'packing_material' => $row['packing_material'],
                            'purity'           => $row['purity'],
                            'inventory'        => $row['inventory'],
                            'brand'            => $row['brand'],
                            'provider'         => $row['provider'],
                            'storage_place'    => $findStoragePlaceId
                        ]);
                    } else {
                        Db::name('chem_product_pack')->insert([
                            'product_id'       => $productId,
                            'pack'             => $row['pack'],
                            'unit'             => $row['unit'],
                            'product_no'       => $row['product_no'],
                            'price'            => floatval($row['price']),
                            'packing_material' => $row['packing_material'],
                            'purity'           => $row['purity'],
                            'inventory'        => $row['inventory'],
                            'brand'            => $row['brand'],
                            'provider'         => $row['provider'],
                            'status'           => 1,//状态;1:发布;0 :下架
                            'storage_place'    => $findStoragePlaceId
                        ]);
                    }
                } catch (\Exception $e) {
                    array_push($logs, ['row_index' => $rowIndex, 'message' => '化合物库存数据写入失败']);
                    continue;
                }

            }
        }

        return ['logs' => $logs, 'total_count' => count($rows)];
    }

    private function getColumnKey($columnIndex, $currentSheet)
    {
        $keys = [
            'CAS'  => 'cas',
            '中文名'  => 'cn_name',
            '英文名'  => 'name',
            '价格'   => 'price',
            '规格'   => 'specification',
            '包装'   => 'packing_material',
            '纯度'   => 'purity',
            '库存'   => 'inventory',
            '品牌'   => 'brand',
            '供应商'  => 'provider',
            '货号'   => 'product_no',
            '位置'   => 'storage_place',
            '产品分类' => 'category_name',
        ];
        static $_columnKeys = [];
        if (empty($_columnKeys)) {
            $highestColumn = $currentSheet->getHighestColumn();
            $allColumn     = \PHPExcel_Cell::columnIndexFromString($highestColumn);

            for ($currentColumnIndex = 0; $currentColumnIndex < $allColumn; $currentColumnIndex++) {
                $currentColumn = \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex);
                $address       = $currentColumn . '1';
                $currentCell   = $currentSheet->getCell($address);
                $value         = trim($currentCell->getValue());
                if (empty($value)) {
                    break;
                } else {
                    if (!empty($keys[$value])) {
                        $_columnKeys[$currentColumnIndex] = $keys[$value];
                    }
                }
            }
        }

        $key = '';
        if (!empty($_columnKeys[$columnIndex])) {
            $key = $_columnKeys[$columnIndex];
        }
        return $key;
    }


}


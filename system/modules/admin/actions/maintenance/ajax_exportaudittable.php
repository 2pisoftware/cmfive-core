<?php

function ajax_exportaudittable_GET(Web $w) {
	
	$audit_rows = $w->db->get('audit')->fetchAll();
	
	$text_csv = '';
	$text_csv .= implode(',', (new Audit($w))->getDbTableColumnNames()) . "\n";
	
	$csv_audit_ids = [];
	if (!empty($audit_rows)) {
		foreach($audit_rows as $audit_row) {
			$text_csv .= rtrim(implode(',', $audit_row), ',') . "\n";
			$csv_audit_ids[]  = $audit_row['id'];
		}
	}
	
	// exec('mkdir -p ' . realpath(FILE_ROOT . 'audit'));
	
	$name = 'audit_export_' . date('YmdHis') . '.csv';

	$file = $w->File->getFileObject($w->File->getFilesystem(FILE_ROOT . 'audit'), $name);
	$file->setContent($text_csv);
	
	// Remove audit rows
	if (!empty($csv_audit_ids)) {
		$w->db->delete('audit')->where('id', $csv_audit_ids)->execute();
	}
	
	header('Content-Description: File Transfer');
	header("Content-Type: text/csv");
	header('Content-Disposition: attachment; filename="' . $name . '"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header("Content-Length: " . $file->getSize());
	
	echo $file->getContent();
	
	exit;
}

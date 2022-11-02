<?php


namespace App\Service;


class ToolsService {

	public function slugify(string $name): string {
		$slug = $this->remove_accents($name);
		$slug = trim($slug);
		$slug = strtolower($slug);
		$slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
		return preg_replace('/-{2,}/', '-', $slug);
	}


	public function remove_accents(string $string): string {
		$search = explode(",", "ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,ø,Ø,Å,Á,À,Â,Ä,È,É,Ê,Ë,Í,Î,Ï,Ì,Ò,Ó,Ô,Ö,Ú,Ù,Û,Ü,Ÿ,Ç,Æ,Œ");
		$replace = explode(",", "c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,o,O,A,A,A,A,A,E,E,E,E,I,I,I,I,O,O,O,O,U,U,U,U,Y,C,AE,OE");
		return str_replace($search, $replace, $string);
	}

}

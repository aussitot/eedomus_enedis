<?php
	/**
     * L o g
     *
     * Module de gestion des fichiers logs
     *
     * @author twitter:@havok
     * @version 2012.03.29
     *
     * private $_fichierlog; Nom du fichier log
     *
     * public function ecrireLog ($message) Ecrit un texte dans le fichier log
     * public function chercheLog ($cherche) Recherche d'un texte dans le log
     * public function lireLog() Retourne le contenu du fichier log
     * public function eraseLog() Supprime le fichier log
     */

class Log {

	private $_fichierlog; /* Nom du fichier log */

	public function __construct ($fichierlog) // Constructeur
        {
            $this->_fichierlog = $fichierlog;
        }

    /**
    * Ecrit un texte dans le fichier log
    * @param (string) $message Texte a ecrire
    * @return (boolean) Cette fonction retourne TRUE en cas de succès ou FALSE si une erreur survient.
    */
    public function ecrireLog ($message)
    	{
    		return error_log($message."\n", 3, $this->_fichierlog);
    	}

    /**
    * Cherche un texte dans le fichier log
    * @param (string) $cherche Texte a rechercher
    * @return (int) position numérique de la première occurrence de cherche ou FALSE si non trouvé
    */
    public function chercheLog ($cherche) //Recherche d'un texte dans le log
    	{
    		$str = file_get_contents($this->_fichierlog);
    		$strYN = strpos($str, $cherche);
    		return $strYN;
    	}

    /**
    * Retourne le contenu du fichier log
    * @return (string) Contenu du fichier log ou FALSE en cas d'erreur
    */
    public function lireLog()
    	{
    		return file_get_contents($this->_fichierlog);
    	}

    public function eraseLog()
    	{
    		return unlink($this->_fichierlog);
    	}
}
?>

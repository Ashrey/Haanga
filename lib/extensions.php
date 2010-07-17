<?php
/*
  +---------------------------------------------------------------------------------+
  | Copyright (c) 2010 Haanga                                                       |
  +---------------------------------------------------------------------------------+
  | Redistribution and use in source and binary forms, with or without              |
  | modification, are permitted provided that the following conditions are met:     |
  | 1. Redistributions of source code must retain the above copyright               |
  |    notice, this list of conditions and the following disclaimer.                |
  |                                                                                 |
  | 2. Redistributions in binary form must reproduce the above copyright            |
  |    notice, this list of conditions and the following disclaimer in the          |
  |    documentation and/or other materials provided with the distribution.         |
  |                                                                                 |
  | 3. All advertising materials mentioning features or use of this software        |
  |    must display the following acknowledgement:                                  |
  |    This product includes software developed by César D. Rodas.                  |
  |                                                                                 |
  | 4. Neither the name of the César D. Rodas nor the                               |
  |    names of its contributors may be used to endorse or promote products         |
  |    derived from this software without specific prior written permission.        |
  |                                                                                 |
  | THIS SOFTWARE IS PROVIDED BY CÉSAR D. RODAS ''AS IS'' AND ANY                   |
  | EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED       |
  | WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE          |
  | DISCLAIMED. IN NO EVENT SHALL CÉSAR D. RODAS BE LIABLE FOR ANY                  |
  | DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES      |
  | (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;    |
  | LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND     |
  | ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT      |
  | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS   |
  | SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE                     |
  +---------------------------------------------------------------------------------+
  | Authors: César Rodas <crodas@php.net>                                           |
  +---------------------------------------------------------------------------------+
*/

Abstract Class Haanga_Extensions
{
    private static $_instances;

    final private function __construct()
    {
    }

    final static function getInstance($name)
    {
        if (!class_exists($name)) {
            throw new Haanga_CompilerException("{$name} is not a class");
        }
        if (!is_subclass_of($name, __CLASS__)) {
            throw new Haanga_CompilerException("{$name} is not a sub-class of ".__CLASS__);
        }

        if (!isset(self::$_instances[$name])) {
            self::$_instances[$name] = new $name;
        }
        return self::$_instances[$name];
    }

    abstract function isValid($name);
    abstract function getClassName($name);

    function getFilePath($file, $rel=TRUE, $pref=NULL)
    {
        if (!$pref) {
            $pref = strtolower(get_class($this));
        }
        $file = "/{$pref}/{$file}.php";
        if ($rel) {
            $file = HAANGA_DIR.$file;
        }
        return $file;
    }

    final public function getFunctionAlias($name)
    {
        if (!$this->isValid($name)) {
            return NULL;
        }
        $zclass     = $this->getClassName($name);
        $properties = get_class_vars($zclass);
        if (isset($properties['php_alias'])) {
            return $properties['php_alias'];
        }
        return NULL;
    }

    // generator(string $name, Haanga_Compiler $compiler, Array $args) {{{
    /**
     *  Executer the generator method of the extension. If 
     *  the extension doesn't has any generator method, an empty
     *  will be returned.
     *
     *  @param string       $name extension name
     *  @param Haanga_Compiler  Compiler object
     *  @param array        Arrays
     *  @param mixed        Extra param
     *
     *  @return array
     */
    function generator($name, Haanga_Compiler $compiler, $args, $extra=NULL)
    {
        if (!$this->hasGenerator($name)) {
            return array();
        }
        $zclass = $this->getClassName($name);
        return $zclass::generator($compiler, $args, $extra);
    }
    // }}}

    // hasGenerator(string $name) {{{
    /** 
     *  Return TRUE if the extension has a  
     *  generator method
     *
     *  @param string $name Extension name
     *
     *  @return bool
     */
    function hasGenerator($name)
    {
        if (!$this->isValid($name)) {
            return NULL;
        }
        $zclass = $this->getClassName($name);
        return is_callable(array($zclass, 'generator'));
    }
    // }}}

    // getFunctionBody(string $name, string $name) {{{
    /**
     *  Return the body function of the custom extension main method.
     *
     *  @param string $name
     *  @param string $name
     *
     *  @return string
     */
    static function getFunctionBody($name, $name)
    {
        if (!$this->isValid($name)) {
            return NULL;
        }
        $zclass     = $this->getClassName($name);
        if (!is_callable(array($zclass, 'main'))) {
            throw new Haanga_CompilerException("{$name}: missing main method in {$zclass} class");
        }
        
        $reflection = new ReflectionMethod($zclass, 'main');
        $content    = file($this->getFilePath($name));

        $start   = $reflection->getStartLine()-1;
        $end     = $reflection->getEndLine();
        $content = array_slice($content, $start, $end-$start); 

        $content[0] = str_replace("main", $name, $content[0]);

        return implode("", $content);
    }
    // }}}

}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: sw=4 ts=4 fdm=marker
 * vim<600: sw=4 ts=4
 */
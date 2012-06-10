<?php
    Loader::object('text');

    class DbcontentObject extends TextObject
    {
        public function render(Signature $sig, $image, array $config)
        {
            $value = $config['default'];
            try
            {
                $database = $sig->getDatabase($config['database']);
                $statement = $database->prepare($config['query']);
                $statement->execute(array($sig->getParam('player')));
                $result = $statement->fetch(PDO::FETCH_NUM);
                if (is_array($result))
                {
                    $value = $result[0];
                }
                elseif ($config['required'])
                {
                    throw new RenderException('A required database entry could not be found!');
                }
                $statement->closeCursor();
                unset($result);
                unset($statement);
            }
            catch (PDOException $e)
            {}
            $config['text'] = $value;
            parent::render($sig, $image, $config);
        }

        public function requiredOptions()
        {
            return array('database', 'query', 'font', 'size', 'position', 'color');
        }
    }
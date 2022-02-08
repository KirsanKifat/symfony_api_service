## Service

#### Абстрактный класс имеющий базовые методы get getIn create edit delete
#### Методы get, getIn, create, edit, имеют параметры:
##### - array|object $params массив, либо объект со свойствами являющимися фильтрами для поиска|параметрами для создания|изменения, для преобразования используется EntityObjectSerializer
##### - string $returnType тип возвращаемого объекта, по умолчанию возвращает entityName объект, для преобразования используется EntityObjectSerializer

#### При наслоедованнии от абстрактного класса Service необходимо объявить конструктор класса, который имеет следующие параметры:
##### - EntityManagerInterface $em
##### - LoggerInterface $logger
##### - string $entityName - класс entity сервисом которого является данный класс (этот параметр необходимо переопределить в конструкторе класса)
##### - array|null $uniqueParams - массив имен параметров которые являются уникальными, чья уникальность будет проверяться при использовании методов create и edit (возвращает ошибку ValidationUniqueException), (этот параметр необходимо переопределить в конструкторе класса)

## ObjectSerializer
#### Сериализатор использующий в своей основе jsm serializer, фиксит такие проблемы его как:
#### Невозможность десериализовать объект с неинициализированными свойствами
#### Имеет гибкую модель установки null значения в результат выполнения методов
#### Так же имеет метод обновления одного объекта из другого не конвертируя в массив и обратно (полезно для сущностей Doctrine)

## EntityObjectSerializer 
#### Преобразует массив в объект, обновляя все свойства до сущностей doctrine (головной объект не является таковой)
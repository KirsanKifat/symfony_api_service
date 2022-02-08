Service

базовый класс сервиса, имеющий публичные методы get getIn create edit delete
Все методы имеют параметры:
- array|object $params массив, либо объект со свойствами являющимися фильтрами для поиска|параметрами для создания|изменения,
               для преобразования используется EntityObjectSerializer
- string $returnType тип возвращаемого объекта, по умолчанию возвращает entityName объект, для преобразования используется EntityObjectSerializer

конструктор класса имеет следующие параметры:
  EntityManagerInterface $em
  LoggerInterface $logger
  string $entityName - класс entity сервисом которого является данный класс
  array|null $uniqueParams - массив имен параметров которые являются уникальными, чья уникальность будет проверяться при использовании
                             методов create и edit (возвращает ошибку ValidationUniqueException)

EntityObjectSerializer
преобразует объект в массив и наоборот независимо от приватности свойств, 



id: 1554,
name: "kek",
role: 14
Или это и так работает Оо??? чекнуть этот пункт и возможно выпилить

ExceptionListener и набор Exceptions
добавить возможность в конфиге включать обработчик который будет отлавливать все ошибки и обрабатывать стандартно?? или забить, 
и каждый раз перетаскивать

Сделать отдельную библиотеку с CaseConverter имеет возможность конвертации
'camelCase', 'snake_case', и методы convert, convertArrayKeys, с параметрами

string $from - case из которого конвертируется
string $to - case в который конвертируется
array $data
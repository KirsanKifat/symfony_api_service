<?php

namespace KirsanKifat\ApiServiceBundle\Service;

interface ServiceInterface
{
    /**
     * @param array|object $params
     * @param string|null $returnType
     * @return object
     */
    public function get($params, string $returnType = null): ?object;

    /**
     * @param array|object $params
     * @param string|null $returnType
     * @return object[]
     */
    public function getIn($params, string $returnType = null): array;

    /**
     * @param array|object $params
     * @return int
     */
    public function count($params): int;

    /**
     * @param array|object $params
     * @param string|null $returnType
     * @return object
     */
    public function create($params, string $returnType = null): object;

    /**
     * @param array|object $params
     * @param string|null $returnType
     * @return object
     */
    public function edit($params, string $returnType = null): object;

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id): void;
}
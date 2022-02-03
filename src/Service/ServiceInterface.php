<?php

namespace KirsanKifat\ApiServiceBundle\Service;

interface ServiceInterface
{
    /**
     * @param array|object $params
     * @param string $returnType
     * @return object
     */
    public function get($params, string $returnType): object;

    /**
     * @param array|object $params
     * @param string $returnType
     * @return object[]
     */
    public function getIn($params, string $returnType): array;

    /**
     * @param array|object $params
     * @param string $returnType
     * @return object
     */
    public function create($params, string $returnType): object;

    /**
     * @param array|object $params
     * @param string $returnType
     * @return object
     */
    public function edit($params, string $returnType): object;

    /**
     * @param array|object $params
     * @return mixed
     */
    public function delete($params): void;
}
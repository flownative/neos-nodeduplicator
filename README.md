# Flownative.NodeDuplicator

Copy nodes from one dimension to another to create a base for translating for example or to change your dimensions around.

## Installation

Install the package via composer:

```
composer require flownative/nodeduplicator
```

## Usage

```
./flow node:adopt <rootNodeByPathOrIdentifier> <dimensionStringToCopyFrom> <dimensionStringToCopyTo> [<workspaceName (default to "live")>]
```

An actual example for the demo site is:

```
./flow node:adopt "/sites/neosdemo" "language=en_US" "language=dk"
```

This would start with the homepage of the demo site, use the dimension values `language => en_US` as base for the copy
and `language=dk` as target for the copy. As the workspaceName was omitted it defaults to `live`.




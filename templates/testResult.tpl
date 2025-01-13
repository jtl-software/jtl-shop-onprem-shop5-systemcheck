{$result = $test->getResult()}
{if $result === 0}
    <span class="d-none d-sm-block">
        <button type="button" class="btn btn-test-result btn-success btn-xs">
            <i class="fa-solid fa-check"></i>
        </button>
        <p>{$test->getCurrentState()}</p>
    </span>
    <span class="d-block d-sm-none">
        <button type="button" class="btn btn-test-result btn-success btn-xs">
            <i class="fa-solid fa-check"></i>
        </button>
    </span>
{elseif $result === 1}
    {if $test->getIsOptional()}
        <span class="d-none d-sm-block">
            {if $test->getIsRecommended()}
                <button type="button" class="btn btn-test-result btn-warning btn-xs">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </button>
            {else}
                <button type="button" class="btn btn-test-result btn-primary btn-xs">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            {/if}
            <p>{$test->getCurrentState()}</p>
        </span>
        <span class="d-block d-sm-none">
            {if $test->getIsRecommended()}
                <button type="button" class="btn btn-test-result btn-warning btn-xs">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </button>
            {else}
                <button type="button" class="btn btn-test-result btn-primary btn-xs">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            {/if}
        </span>
    {else}
        <span class="d-none d-sm-block">
            <button type="button" class="btn btn-test-result btn-danger btn-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <p>{$test->getCurrentState()}</p>
        </span>
        <span class="d-block d-sm-none">
            <button type="button" class="btn btn-test-result btn-danger btn-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </span>
    {/if}
{elseif $result === 2}
{/if}

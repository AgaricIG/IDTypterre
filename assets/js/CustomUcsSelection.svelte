<script>

  /*

  This is an example of overriding default module comportment
  This file will replace the original component with the correct webpack configuration

   */
  import { createEventDispatcher } from 'svelte';

  const dispatch = createEventDispatcher();

  export let ucs;
  export let ucs_list;
  export let options;

  console.log('custom');

  let changing = false;

  function selecting(ev) {
    dispatch('selection', {ucs: ucs});
  }

  function change() {
    changing = true;
  }

  function changed() {
    changing = false;
  }

  function close() {
    dispatch('close');
  }

</script>

<div class="apisol-body-ucs-choice">
  <div class="apisol-body-title">
      <big>➢</big> Vous êtes dans l'Unité Cartographique de Sol suivante :
  </div>
  {#if changing == false }
  <div class="apisol-highlighted apisol-highlighted-ucs">
    <span>
    &laquo;{ ucs.name }&raquo;
    {#if options.ChangeUCS === true && ucs_list.length > 1 }
      <a href="/#" class="apisol-link" on:click|preventDefault={change}>Choisir une unité de sol différente</a>
    {/if}
    </span>
    <span class="apisol-no-utx">UCS { ucs.id }</span>
    <div class="apisol-links">
    {#if ucs.externallink }
    <a href="{ ucs.externallink }" class="apisol-link-info">Lien externe</a>
    {/if}
    {#if ucs.ressourcelink }
    <a href="{ ucs.ressourcelink }" class="apisol-link-pdf" target="_blank">Fiche decriptive</a>
    {/if}
    </div>
  </div>
  {/if}
  {#if changing == true}
  <div class="apisol-highlighted apisol-highlighted-ucs">
    <div class="apisol-ucs-option-form">
      <select class="apisol-select-ucs" bind:value="{ucs}" on:blur={changed}>
        <option value="{null}" disabled>Choississez dans la liste suivante...</option>
        {#each ucs_list as u}
        <option value="{ u }">{ u.name }</option>
        {/each}
      </select>
    </div>
  </div>
  {/if}
  <div class="apisol-buttons" style="margin-bottom:0;">
    <button type="button" class="apisol-btn apisol-btn-primary apisol-btn-menu" on:click="{selecting}">Identifiez le type de sol</button>
  </div>
  <div class="apisol-buttons">
    <button type="button" class="apisol-btn-close" on:click="{close}">Fermer</button>
  </div>
</div>


<style>

</style>
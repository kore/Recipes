{use $model}
{tr_context "recipes"}
<?xml version="1.0"?>
<article xmlns="http://docbook.org/ns/docbook">
  <section>
    <title>{$model->title}</title>
    <para>{tr "for %count persons" vars "count" => $model->amount}</para>
    <para>{$model->description}</para>
    <section>
      <title>{tr "Ingredients"}</title>
{foreach $model->ingredients as $name => $ingredients}
      <section>
        <title>{$name}</title>
        <itemizedlist>
    {foreach $ingredients as $ingredient}
          <listitem><para>{$ingredient['amount']} {$ingredient['unit']} {$ingredient['ingredient']}</para></listitem>
    {/foreach}
        </itemizedlist>
      </section>
{/foreach}
    </section>
    <section>
      <title>{tr "Instructions"}</title>
      <variablelist>
        <varlistentry>
          <term>{tr "Preparation time"}</term><listitem><para>{$model->preparation} {tr "minutes"}</para></listitem>
        </varlistentry>
        <varlistentry>
          <term>{tr "Cooking time"}</term><listitem><para>{$model->cooking} {tr "minutes"}</para></listitem>
        </varlistentry>
      </variablelist>
      {raw $model->docbookBody}
    </section>
  </section>
</article>

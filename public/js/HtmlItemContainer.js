class HtmlItemContainer {
  constructor(jContainer) {
    this.container = jContainer;
  }

  append(items) {

    items.forEach(item => {
      this.container.append(item);
    });
    return this;
  }

  replaceWith(html) {
    this.container.html(html);
    return this;
  }

  updateOrAppend(idData, item) {
    const existingItem = this.container.find(`[data-id='${idData.id}']`);
    if (existingItem.length > 0) {
      existingItem.closest('.ic-item').replaceWith(item);
    } else {
      this.append([item]);
    }
    return this;
  }
}

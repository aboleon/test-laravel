class ItemContainer {
  constructor(jContainer, jTemplate) {
    this.container = jContainer;
    this.template = jTemplate.html();
  }

  append(items) {

    items.forEach(item => {
      const itemHtml = this._createItemHtml(item);
      this.container.append(itemHtml);
    });
  }

  updateOrAppend(idData, item) {
    const existingItem = this.container.find(`[data-id='${idData.id}']`);
    if (existingItem.length > 0) {
      const itemHtml = this._createItemHtml(item);
      existingItem.closest(".ic-item").replaceWith(itemHtml);
    } else {
      this.append([item]);
    }
  }

  _createItemHtml(item) {
    let itemHtml = this.template;
    for (let key in item) {
      itemHtml = itemHtml.replace(new RegExp(`{%${key}%}`, 'g'), item[key]);
    }
    return itemHtml;
  }
}

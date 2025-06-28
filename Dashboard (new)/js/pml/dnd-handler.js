export function initDndHandler(originalSlot, answerContainer) {
  let dragged = false;
  let startX = 0, startY = 0;
  const DRAG_THRESHOLD = 16;

  let currentContainer = null;
  let originalContainer = null;

  const placeholder = document.createElement('div');

  // Function to dispatch custom event with selected values
  function updateSelected() {
    const selected = Array.from(answerContainer.querySelectorAll('.answer-box'))
        .map(el => el.dataset.answer);
    
    // Dispatch custom event that Vue can listen to
    document.dispatchEvent(new CustomEvent('dnd-updated', {
      detail: { selected }
    }));
  }

  function onStart(e) {
    const box = e.currentTarget;
    dragged = false;

    const event = e.type === "touchstart" ? e.touches[0] : e;
    startX = event.clientX;
    startY = event.clientY;

    box.classList.add("dragging");

    const rect = box.getBoundingClientRect();
    const offsetX = event.clientX - rect.left;
    const offsetY = event.clientY - rect.top;

    function onMove(e) {
      const event = e.type === "touchmove" ? e.touches[0] : e;
      const dx = event.clientX - startX;
      const dy = event.clientY - startY;

      if (!dragged && Math.sqrt(dx * dx + dy * dy) > DRAG_THRESHOLD) {
        dragged = true;
        originalContainer = box.parentNode;

        placeholder.style.width = rect.width + 'px';
        placeholder.style.height = rect.height + 'px';
        originalContainer.insertBefore(placeholder, box);

        box.style.position = 'absolute';
        box.style.zIndex = '1000';
        box.style.width = rect.width + 'px';
        box.style.height = rect.height + 'px';
        box.style.left = (event.clientX - offsetX) + 'px';
        box.style.top = (event.clientY - offsetY) + 'px';

        document.body.appendChild(box);
      }

      if (dragged) {
        box.style.left = (event.clientX - offsetX) + 'px';
        box.style.top = (event.clientY - offsetY) + 'px';

        const containers = [originalSlot, answerContainer];
        let found = false;

        for (const c of containers) {
          const cRect = c.getBoundingClientRect();
          if (
            event.clientX >= cRect.left && event.clientX <= cRect.right &&
            event.clientY >= cRect.top && event.clientY <= cRect.bottom
          ) {
            if (currentContainer !== c) {
              currentContainer = c;
            }
            found = true;
            reorderPlaceholder(c, event);
            break;
          }
        }

        if (!found) {
          if (placeholder.parentNode) placeholder.remove();
          currentContainer = null;
        }
      }
    }

    function onEnd() {
      document.removeEventListener('mousemove', onMove);
      document.removeEventListener('mouseup', onEnd);
      document.removeEventListener('touchmove', onMove);
      document.removeEventListener('touchend', onEnd);

      box.classList.remove("dragging");

      if (!dragged) {
        // Add the same moving check as vanilla version
        if (box.classList.contains("moving")) return;

        const oldRect = box.getBoundingClientRect();
        // FIX: Use ID-based comparison like vanilla version
        const newParent = box.parentNode.id === originalSlot.id ? answerContainer : originalSlot;

        placeholder.remove();
        newParent.appendChild(box);

        const newRect = box.getBoundingClientRect();
        const dx = oldRect.left - newRect.left;
        const dy = oldRect.top - newRect.top;

        box.style.transform = `translate(${dx}px, ${dy}px)`;
        box.offsetHeight;

        requestAnimationFrame(() => {
          box.classList.add("moving");
          box.style.transform = "translate(0, 0)";
        });

        box.addEventListener("transitionend", () => {
          box.classList.remove("moving");
          box.style.transform = "";
        }, { once: true });

        if (newParent === answerContainer) {
          box.classList.add('answer-active');
        } else {
          box.classList.remove('answer-active');
        }

        // Update after click-to-move
        updateSelected();
        return;
      }

      if (currentContainer && placeholder.parentNode === currentContainer) {
        currentContainer.replaceChild(box, placeholder);
      } else {
        if (placeholder.parentNode) placeholder.remove();
        originalContainer.appendChild(box);
      }

      if (box.parentNode === answerContainer) {
        box.classList.add('answer-active');
      } else {
        box.classList.remove('answer-active');
      }

      box.style.position = '';
      box.style.left = '';
      box.style.top = '';
      box.style.zIndex = '';
      box.style.width = '';
      box.style.height = '';

      dragged = false;
      currentContainer = null;
      originalContainer = null;
      
      // Update after drag operation
      updateSelected();
    }

    document.addEventListener('mousemove', onMove);
    document.addEventListener('mouseup', onEnd);
    document.addEventListener('touchmove', onMove, { passive: false });
    document.addEventListener('touchend', onEnd);
    e.preventDefault();
  }

  function reorderPlaceholder(container, event) {
    const children = Array.from(container.children).filter(child => child !== placeholder);
    const beforeRects = children.map(child => child.getBoundingClientRect());

    let insertedIndex = -1;
    for (let i = 0; i < children.length; i++) {
      const rect = children[i].getBoundingClientRect();
      const childCenterX = rect.left + rect.width / 2;
      if (event.clientX < childCenterX) {
        container.insertBefore(placeholder, children[i]);
        insertedIndex = i;
        break;
      }
    }
    if (insertedIndex === -1) {
      container.appendChild(placeholder);
    }

    const afterChildren = Array.from(container.children).filter(child => child !== placeholder);
    const afterRects = afterChildren.map(child => child.getBoundingClientRect());

    afterChildren.forEach((child, index) => {
      const before = beforeRects[index], after = afterRects[index];
      if (!before || !after) return;

      const deltaX = before.left - after.left;
      const deltaY = before.top - after.top;

      if (deltaX !== 0 || deltaY !== 0) {
        child.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
        child.style.transition = 'none';
        child.offsetHeight;
        child.style.transition = 'transform 0.3s ease';
        child.style.transform = 'translate(0, 0)';
        child.addEventListener('transitionend', () => {
          child.style.transition = '';
          child.style.transform = '';
        }, { once: true });
      }
    });
  }

  // Attach drag start to all draggable boxes inside the containers
  const allBoxes = [...originalSlot.querySelectorAll('.answer-box'), ...answerContainer.querySelectorAll('.answer-box')];
  allBoxes.forEach(box => {
    box.addEventListener('mousedown', onStart);
    box.addEventListener('touchstart', onStart, { passive: false });
    box.ondragstart = () => false;
  });

  // Initialize the selected array
  updateSelected();
}
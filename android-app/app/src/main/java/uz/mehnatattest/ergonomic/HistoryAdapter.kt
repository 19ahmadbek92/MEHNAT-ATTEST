package uz.mehnatattest.ergonomic

import android.graphics.Color
import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import uz.mehnatattest.ergonomic.databinding.ItemHistoryBinding

class HistoryAdapter(
    private val items: List<HistoryRow>,
    private val onClick: (HistoryRow) -> Unit,
) : RecyclerView.Adapter<HistoryAdapter.ViewHolder>() {

    inner class ViewHolder(val binding: ItemHistoryBinding) : RecyclerView.ViewHolder(binding.root)

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemHistoryBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val item = items[position]
        holder.binding.tvLoadDescription.text = item.loadDescription ?: "—"
        holder.binding.tvMeta.text = "${item.createdAt} · LI=${item.liftingIndex} · Klass ${item.severityClass}"
        holder.binding.tvRiskCategory.text = item.riskCategory
        holder.binding.tvIntegral.text = "${item.integralSafetyIndex}"

        val color = when {
            item.integralSafetyIndex >= 70 -> Color.parseColor("#176B3A")
            item.integralSafetyIndex >= 40 -> Color.parseColor("#B8841E")
            else -> Color.parseColor("#B83232")
        }
        holder.binding.tvIntegral.setTextColor(color)
        holder.binding.tvRiskCategory.setTextColor(color)

        holder.itemView.setOnClickListener { onClick(item) }
    }

    override fun getItemCount(): Int = items.size
}
